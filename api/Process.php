<?php

namespace app\extensions\fkgupload\api;

use \app\conf\App;
use \app\inc\Response;
use \app\models\Database;
use \app\conf\Connection;
use \app\inc\Session;
use \app\inc\Input;
use \app\inc\Model;
use \app\models\Table;

/**
 * Class Processvector
 * @package app\controllers\upload
 */
class Process extends \app\inc\Controller
{
    private $model;

    function __construct()
    {

        Session::start();
        Session::authenticate(null);

        Database::setDb(Session::getUser());
        Connection::$param["postgisschema"] = $_SESSION['postgisschema'];

        $this->model = new Model();

    }

    private function fkgSchema($schema)
    {
        $schemata = [
            "t_5710_born_skole_dis" => [
                "objekt_id" => ["objekt_id", false, "uuid"],
                "cvr_kode" => ["cvr_kode", true, "int"],
                "bruger_id" => ["bruger_id", true, "varchar"],
                "oprindkode" => ["oprindkode", true, "int"],
                "statuskode" => ["statuskode", true, "int"],
                "off_kode" => ["off_kode", true, "int"],
                "udd_distrikt_nr" => ["udd_d_nr", false, "int"],
                "udd_distrikt_navn" => ["udd_d_nv", false, "varchar"],
                "udd_distrikt_type_kode" => ["udd_tpkode", false, "int"],
                "starttrin_kode" => ["strtr_kode", false, "int"],
                "sluttrin_kode" => ["slutr_kode", false, "int"],
                "noegle" => ["noegle", false, "varchar"],
                "sagsnr" => ["sagsnr", false, "varchar"],
                "link" => ["link", false, "varchar"],
                "geometri" => ["the_geom", true, "geometry"],
            ]
        ];

        return $schemata[$schema];
    }

    private function schema(string $uploadTable, string $fkgTable): array
    {
        $response = [];
        $table = new Table($uploadTable);
        $uploadSchema = $table->getTableStructure()["data"];
        $fkgSchema = $this->fkgSchema($fkgTable);
        $arr1 = [];
        $arr2 = [];
        $arr3 = [];
        $sqlInsertIds = [];
        $sqlUpdateIds = [];
        $checkFields = false;
        $missingField = "";

        //$response["data"]["upload_schema"] = $uploadSchema;
        //$response["data"]["fkg_schema"][] = $fkgSchema;


        foreach ($fkgSchema as $key => $value) {
            $check = false;
            foreach ($uploadSchema as $sValue) {
                if ($value[0] == $sValue["id"]) {
                    $response["data"]["fields"][$value[0]] = true;
                    if ($key != "objekt_id") {
                        $arr1[] = $key;
                        $arr2[] = $value[0] . "::" . $value[2];
                    }
                    $arr3[] = "{$key}={$uploadTable}.{$value[0]}::{$value[2]}";
                    $check = true;
                    break;
                }

            }
            if (!$check) {
                $response["data"]["fields"][$value[0]] = false;
                if ($value[1]) {
                    $checkFields = true;
                    $missingField = $value[0];
                    break;
                }
            }
        }

        if ($checkFields) {
            $response['success'] = false;
            $response['code'] = 401;
            $response['message'] = "Obligatorisk felt '{$missingField}' mangler";
            return $response;
        }

        $this->model->connect();
        $this->model->begin();

        $sql = "SELECT * FROM {$uploadTable}";
        $res = $this->model->prepare($sql);
        try {
            $res->execute();
        } catch (\PDOException $e) {
            $response['success'] = false;
            $response['message'] = $e->getMessage();
            $response['code'] = 401;
            return $response;
        }
        while ($row = $this->model->fetchRow($res, "assoc")) {
            if (isset($row["objekt_id"]) && $row["objekt_id"]) {
                $sqlUpdateIds[] = $row["objekt_id"];
            } else {
                $sqlInsertIds[] = $row["gid"];
            }
        }

        $sqlUpdate = "UPDATE fkg." . $fkgTable . " SET " . implode(", ", $arr3) . " FROM " . $uploadTable . " WHERE fkg." . $fkgTable . ".objekt_id=" . $uploadTable . ".objekt_id::uuid AND " . $fkgTable . ".objekt_id=:objekt_id";
        //echo $sqlUpdate . "\n\n";
        $resUpdate = $this->model->prepare($sqlUpdate);

        $response["data"]["updated_ids"] = [];
        foreach ($sqlUpdateIds as $objekt_id) {
            try {
                $resUpdate->execute(["objekt_id" => $objekt_id]);
            } catch (\PDOException $e) {
                $response['success'] = false;
                $response['message'][] = $e->get;
                $response['code'] = 401;
                return $response;
            }

            $response["data"]["updated_ids"][] = $objekt_id;
        }

        $sqlInsert = "INSERT INTO fkg." . $fkgTable . " (" . implode(",", $arr1) . ") (SELECT " . implode(",", $arr2) . " FROM " . $uploadTable . " WHERE gid=:gid) RETURNING objekt_id";
        //echo $sqlInsert . "\n\n";
        $resInsert = $this->model->prepare($sqlInsert);

        $response["data"]["inserted_ids"] = [];
        foreach ($sqlInsertIds as $gid) {
            try {
                $resInsert->execute(["gid" => $gid]);
            } catch (\PDOException $e) {
                $response['success'] = false;
                $response['message'][] = explode("\n", $e->getMessage())[0];
                $response['message'][] = explode("\n", $e->getMessage())[1];
                //$response['message'][] = $e->getMessage();
                $response['code'] = 401;
                return $response;
            }

            $row = $this->model->fetchRow($resInsert, "assoc");
            $response["data"]["inserted_ids"][] = $row["objekt_id"];
        }

        $this->model->commit();

        $response["success"] = true;
        return $response;
    }

    /**
     * @return array
     */
    public function get_index()
    {

        $response = [];
        $dir = App::$param['path'] . "app/tmp/" . Connection::$param["postgisdb"] . "/__vectors";
        $safeName = Session::getUser() . "_" . md5(microtime() . rand());
        $encoding = $_REQUEST["encoding"];
        $fkgName = $_REQUEST["fkgname"];

        switch ($fkgName) {
            case "t_5710_born_skole_dis":
                $geoType = "multipolygon";
                break;
            default:
                $geoType = "auto";
                break;

        }

        if (is_numeric($safeName[0])) {
            $safeName = "_" . $safeName;
        }


        // Check if file is .zip
        // =====================
        $zipCheck1 = explode(".", $_REQUEST['file']);
        $zipCheck2 = array_reverse($zipCheck1);
        $format = strtolower($zipCheck2[0]);
        if ($format == "zip" || $format == "rar") {
            $ext = array("shp", "tab", "geojson", "gml", "kml", "mif", "gdb", "csv");
            $folderArr = array();
            $safeNameArr = array();
            for ($i = 0; $i < sizeof($zipCheck1) - 1; $i++) {
                $folderArr[] = $zipCheck1[$i];
            }
            $folder = implode(".", $folderArr);

            // ZIP start
            // =========
            if ($format == "zip") {
                $zip = new \ZipArchive;
                $res = $zip->open($dir . "/" . $_REQUEST['file']);
                if ($res === false) {
                    $response['success'] = false;
                    $response['message'] = "Could not unzip file";
                    return Response::json($response);
                }
                $zip->extractTo($dir . "/" . $folder);
                $zip->close();
            }

            // RAR start
            // =========
            if ($format == "rar") {
                $rar_file = rar_open($dir . "/" . $_REQUEST['file']);
                if (!$rar_file) {
                    $response['success'] = false;
                    $response['message'] = "Could not unrar file";
                    return Response::json($response);
                }

                $list = rar_list($rar_file);
                foreach ($list as $file) {
                    $entry = rar_entry_get($rar_file, $file);
                    $file->extract($dir . "/" . $folder); // extract to the current dir
                }
                rar_close($rar_file);
            }

            if ($handle = opendir($dir . "/" . $folder)) {
                while (false !== ($entry = readdir($handle))) {
                    if ($entry !== "." && $entry !== "..") {
                        $zipCheck1 = explode(".", $entry);
                        $zipCheck2 = array_reverse($zipCheck1);
                        if (in_array($format, $ext)) {
                            $_REQUEST['file'] = $folder . "/" . $entry;
                            for ($i = 0; $i < sizeof($zipCheck1) - 1; $i++) {
                                $safeNameArr[] = $zipCheck1[$i];
                            }
                            $safeName = Model::toAscii(implode(".", $safeNameArr), array(), "_");
                            break;
                        }
                        $_REQUEST['file'] = $folder;
                    }
                }
            }
        }

        switch ($geoType) {
            case "point":
                $type = "point";
                break;
            case "linestring":
                $type = "linestring";
                break;
            case "polygon":
                $type = "polygon";
                break;
            case "multipoint":
                $type = "multipoint";
                break;
            case "multilinestring":
                $type = "multilinestring";
                break;
            case "multipolygon":
                $type = "multipolygon";
                break;
            case "geometry":
                $type = "geometry";
                break;
            default:
                $type = "PROMOTE_TO_MULTI";
                break;
        }

        $cmd = "PGCLIENTENCODING={$encoding} ogr2ogr " .
            "-overwrite " .
            "-lco 'GEOMETRY_NAME=the_geom' " .
            "-lco 'FID=gid' " .
            "-lco 'PRECISION=NO' " .
            "-a_srs 'EPSG:25832' " .
            "-dim XY " .

            "-f 'PostgreSQL' PG:'host=" . Connection::$param["postgishost"] . " user=" . Connection::$param["postgisuser"] . " password=" . Connection::$param["postgispw"] . " dbname=" . Connection::$param["postgisdb"] . "' " .
            "'" . $dir . "/" . $_REQUEST['file'] . "' " .
            "-nln " . Connection::$param["postgisschema"] . ".{$safeName} " .
            "-nlt {$type}";

        exec($cmd . ' 2>&1', $out, $err);

        // Check ogr2ogr output
        // ====================
        if ($out[0] == "") {

            // Bust cache, in case of layer already exist
            // ==========================================
            \app\controllers\Tilecache::bust(Connection::$param["postgisschema"] . "." . $safeName);


        } else {
            $response['success'] = false;
            $response['message'] = Session::createLog($out, $_REQUEST['file']);
            $response['out'] = $out;
            Session::createLog($out, $_REQUEST['file']);

            // Make sure the table is dropped if not
            // skipping failures and it didn't exists before
            // =================================================
            $sql = "DROP TABLE " . Connection::$param["postgisschema"] . "." . $safeName;
            $res = $this->model->prepare($sql);
            try {
                $res->execute();
            } catch (\PDOException $e) {

            }

            return $response;
        }

        //$response['cmd'] = $cmd;

        $s = $this->schema("public." . $safeName, $fkgName);

        if (!$s["success"]) {
            $response['success'] = false;
            $response['message'] = $s["message"];
            $response['code'] = "401";
        } else {
            $response['message'] = "Data indlæst";
            $response['success'] = true;
        }

        $response["fkg_report"] = $s["data"];

        return $response;
    }
}