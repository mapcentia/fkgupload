<?php
/**
 * @author     Martin Høgh <mh@mapcentia.com>
 * @copyright  2013-2021 MapCentia ApS
 * @license    http://www.gnu.org/licenses/#AGPL  GNU AFFERO GENERAL PUBLIC LICENSE 3
 *
 */

namespace app\extensions\fkgupload\api;

use app\conf\App;
use app\controllers\Tilecache;
use app\inc\Controller;
use app\inc\Input;
use app\models\Database;
use app\conf\Connection;
use app\inc\Session;
use app\inc\Model;
use app\inc\UserFilter;
use app\models\Geofence as GeofenceModel;
use app\models\Table;
use PDOException;
use Phpfastcache\Exceptions\PhpfastcacheInvalidArgumentException;
use ZipArchive;


/**
 * Class Processvector
 * @package app\controllers\upload
 */
class Process extends Controller
{
    /**
     * @var Model
     */
    private $model;

    function __construct()
    {
        parent::__construct();

        Session::start();
        Session::authenticate(null);

        Database::setDb(Session::getDatabase());
        Connection::$param["postgisschema"] = $_SESSION['postgisschema'];

        $this->model = new Model();

        // Set path so libjvm.so can be loaded in ogr2ogr for MS Access support
        putenv("LD_LIBRARY_PATH=/usr/lib/jvm/java-8-openjdk-amd64/jre/lib/amd64/server");
    }

    /**
     * @param int $schema
     * @return array[]
     */
    private function fkgSchema(int $schema): array
    {
        $schemata = [
            5710 => [
                "objekt_id" => ["objekt_id", false, "uuid"],
                "cvr_kode" => ["cvr_kode", true, "int"],
                "bruger_id" => ["bruger_id", true, "varchar"],
                "oprindkode" => ["oprindkode", true, "int"],
                "statuskode" => ["statuskode", true, "int"],
                "off_kode" => ["off_kode", true, "int"],

                "udd_distrikt_nr" => ["udd_d_nr", false, "int"],
                "udd_distrikt_navn" => ["udd_d_nv", false, "varchar"],
                "udd_distrikt_type_kode" => ["udd_tpkode", true, "int"],
                "starttrin_kode" => ["strtr_kode", false, "int"],
                "sluttrin_kode" => ["slutr_kode", false, "int"],

                "noegle" => ["noegle", false, "varchar"],
                "sagsnr" => ["sagsnr", false, "varchar"],
                "link" => ["link", false, "varchar"],
                "geometri" => ["the_geom", true, "geometry"],
            ],
            5800 => [
                "objekt_id" => ["objekt_id", false, "uuid"],
                "cvr_kode" => ["cvr_kode", true, "int"],
                "bruger_id" => ["bruger_id", true, "varchar"],
                "oprindkode" => ["oprindkode", true, "int"],
                "statuskode" => ["statuskode", true, "int"],
                "off_kode" => ["off_kode", true, "int"],

                "facil_ty_k" => ["facil_ty_k", true, "int"],
                "navn" => ["navn", false, "varchar"],
                "beskrivels" => ["beskrivels", false, "varchar"],
                "lang_beskr" => ["lang_beskr", false, "varchar"],
                "uk_k_beskr" => ["uk_k_beskr", false, "varchar"],
                "uk_l_beskr" => ["uk_l_beskr", false, "varchar"],
                "d_k_beskr" => ["d_k_beskr", false, "varchar"],
                "d_l_beskr" => ["d_l_beskr", false, "varchar"],
                "ansvar_org" => ["ansvar_org", false, "varchar"],
                "kontak_ved" => ["kontak_ved", false, "varchar"],

                "handicap_k" => ["handicap_k", false, "int"], // Felt defineret under: 4.1

                "saeson_k" => ["saeson_k", false, "int"],
                "saeson_st" => ["saeson_st", false, "date"],
                "saeson_sl" => ["saeson_sl", false, "date"],
                "doegnaab_k" => ["doegnaab_k", false, "int"],
                "vandhane_k" => ["vandhane_k", false, "int"],
                "bemand_k" => ["bemand_k", false, "int"],
                "betaling_k" => ["betaling_k", false, "int"],
                "book_k" => ["book_k", false, "int"],
                "antal_pl" => ["antal_pl", false, "int"],
                "foto_link1" => ["foto_link1", false, "varchar"],
                "foto_link2" => ["foto_link2", false, "varchar"],
                "film_link" => ["film_link", false, "varchar"],
                "adr_id" => ["adr_id", false, "uuid"],

                "ansva_v_k" => ["ansva_v_k", false, "int"], // Felt defineret under: 4.1
                "link" => ["link", false, "varchar"], // Felt defineret under: 4.1

                "geometri" => ["the_geom", true, "geometry"],
            ],
            5801 => [
                "objekt_id" => ["objekt_id", false, "uuid"],
                "cvr_kode" => ["cvr_kode", true, "int"],
                "bruger_id" => ["bruger_id", true, "varchar"],
                "oprindkode" => ["oprindkode", true, "int"],
                "statuskode" => ["statuskode", true, "int"],
                "off_kode" => ["off_kode", true, "int"],

                "facil_ty_k" => ["facil_ty_k", true, "int"],
                "navn" => ["navn", false, "varchar"],
                "beskrivels" => ["beskrivels", false, "varchar"],
                "lang_beskr" => ["lang_beskr", false, "varchar"],
                "uk_k_beskr" => ["uk_k_beskr", false, "varchar"],
                "uk_l_beskr" => ["uk_l_beskr", false, "varchar"],
                "d_k_beskr" => ["d_k_beskr", false, "varchar"],
                "d_l_beskr" => ["d_l_beskr", false, "varchar"],
                "ansvar_org" => ["ansvar_org", false, "varchar"],
                "kontak_ved" => ["kontak_ved", false, "varchar"],

                "handicap_k" => ["handicap_k", false, "int"], // Felt defineret under: 4.1

                "saeson_k" => ["saeson_k", false, "int"],
                "saeson_st" => ["saeson_st", false, "date"],
                "saeson_sl" => ["saeson_sl", false, "date"],
                "doegnaab_k" => ["doegnaab_k", false, "int"],
                "vandhane_k" => ["vandhane_k", false, "int"],
                "bemand_k" => ["bemand_k", false, "int"],
                "betaling_k" => ["betaling_k", false, "int"],
                "book_k" => ["book_k", false, "int"],
                "antal_pl" => ["antal_pl", false, "int"],
                "foto_link1" => ["foto_link1", false, "varchar"],
                "foto_link2" => ["foto_link2", false, "varchar"],
                "film_link" => ["film_link", false, "varchar"],
                "adr_id" => ["adr_id", false, "uuid"],

                "ansva_v_k" => ["ansva_v_k", false, "int"], // Felt defineret under: 4.1
                "link" => ["link", false, "varchar"], // Felt defineret under: 4.1

                "geometri" => ["the_geom", true, "geometry"],
            ],
            5802 => [
                "objekt_id" => ["objekt_id", false, "uuid"],
                "cvr_kode" => ["cvr_kode", true, "int"],
                "bruger_id" => ["bruger_id", true, "varchar"],
                "oprindkode" => ["oprindkode", true, "int"],
                "statuskode" => ["statuskode", true, "int"],
                "off_kode" => ["off_kode", true, "int"],

                "rute_ty_k" => ["rute_ty_k", true, "int"],
                "rute_uty_k" => ["rute_uty_k", false, "int"],

                "navn" => ["navn", false, "varchar"],
                "navndels" => ["navndels", false, "varchar"],
                "straekn_nr" => ["straekn_nr", false, "varchar"],

                "afm_rute_k" => ["afm_rute_k", false, "int"],
                "laengde" => ["laengde", false, "numeric"],

                "beskrivels" => ["beskrivels", false, "varchar"],
                "lang_beskr" => ["lang_beskr", false, "varchar"],
                "uk_k_beskr" => ["uk_k_beskr", false, "varchar"],
                "uk_l_beskr" => ["uk_l_beskr", false, "varchar"],
                "d_k_beskr" => ["d_k_beskr", false, "varchar"],
                "d_l_beskr" => ["d_l_beskr", false, "varchar"],
                "ansvar_org" => ["ansvar_org", false, "varchar"],
                "kontak_ved" => ["kontak_ved", false, "varchar"],
                "betaling_k" => ["betaling_k", false, "int"],

                "belaegn_k" => ["belaegn_k", false, "int"], // Felt defineret under: 4.1
                "handicap_k" => ["handicap_k", false, "int"], // Felt defineret under: 4.1
                "ansva_v_k" => ["ansva_v_k", false, "int"], // Felt defineret under: 4.1

                "startpkt_x" => ["startpkt_x", false, "int"],
                "startpkt_y" => ["startpkt_y", false, "int"],
                "slutpkt_x" => ["slutpkt_x", false, "int"],
                "slutpkt_y" => ["slutpkt_y", false, "int"],
                "svaerhed_k" => ["svaerhed_k", false, "int"],
                "obs" => ["obs", false, "varchar"],
                "kategori_k" => ["kategori_k", false, "int"],
                "certifi_k" => ["certifi_k", false, "int"],
                "hierarki_k" => ["hierarki_k", false, "int"],
                "folder_k" => ["folder_k", false, "int"],
                "folde_link" => ["folde_link", false, "varchar"],
                "foto_link1" => ["foto_link1", false, "varchar"],
                "foto_link2" => ["foto_link2", false, "varchar"],
                "film_link" => ["film_link", false, "varchar"],
                "gpx_link" => ["gpx_link", false, "varchar"],
                "adr_id" => ["adr_id", false, "uuid"],

                "link" => ["link", false, "varchar"], // Felt defineret under: 4.1
                "geometri" => ["the_geom", true, "geometry"],
            ],
            5713 => [
                "objekt_id" => ["objekt_id", false, "uuid"],
                "cvr_kode" => ["cvr_kode", true, "int"],
                "bruger_id" => ["bruger_id", true, "varchar"],
                "oprindkode" => ["oprindkode", true, "int"],
                "statuskode" => ["statuskode", true, "int"],
                "off_kode" => ["off_kode", true, "int"],

                "prog_distrikt_nr" => ["pro_dis_nr", false, "int"],
                "prog_distrikt_navn" => ["pro_dis_na", false, "varchar"],
                "prog_distrikt_type_kode" => ["pro_di_ty_k", false, "int"],

                "noegle" => ["noegle", false, "varchar"],
                "sagsnr" => ["sagsnr", false, "varchar"],
                "link" => ["link", false, "varchar"],
                "geometri" => ["the_geom", true, "geometry"],
            ],
            5711 => [
                "objekt_id" => ["objekt_id", false, "uuid"],
                "cvr_kode" => ["cvr_kode", true, "int"],
                "bruger_id" => ["bruger_id", true, "varchar"],
                "oprindkode" => ["oprindkode", true, "int"],
                "statuskode" => ["statuskode", true, "int"],
                "off_kode" => ["off_kode", true, "int"],

                "an_distrikt_nr" => ["an_dis_nr", false, "int"],
                "an_distrikt_navn" => ["an_dis_na", false, "varchar"],
                "an_distrikt_type_kode" => ["an_dis_ty_k", false, "int"],

                "noegle" => ["noegle", false, "varchar"],
                "sagsnr" => ["sagsnr", false, "varchar"],
                "link" => ["link", false, "varchar"],
                "geometri" => ["the_geom", true, "geometry"],
            ],
            5712 => [
                "objekt_id" => ["objekt_id", false, "uuid"],
                "cvr_kode" => ["cvr_kode", true, "int"],
                "bruger_id" => ["bruger_id", true, "varchar"],
                "oprindkode" => ["oprindkode", true, "int"],
                "statuskode" => ["statuskode", true, "int"],
                "off_kode" => ["off_kode", true, "int"],

                "plej_distrikt_nr" => ["pl_dis_nr", false, "int"],
                "plej_distrikt_navn" => ["pl_dis_na", false, "varchar"],
                "plej_distrikt_type_kode" => ["pl_dis_ty_k", false, "int"],

                "noegle" => ["noegle", false, "varchar"],
                "sagsnr" => ["sagsnr", false, "varchar"],
                "link" => ["link", false, "varchar"],
                "geometri" => ["the_geom", true, "geometry"],
            ],
        ];

        return $schemata[$schema];
    }

    /**
     * @param int $code
     * @return string
     */
    private function getThemeName(int $code): string
    {
        $names = [
            5710 => "t_5710_born_skole_dis",
            5800 => "t_5800_fac_pkt",
            5801 => "t_5801_fac_fl",
            5802 => "t_5802_fac_li",
            5713 => "t_5713_prog_stat_dis",
            5711 => "t_5711_and_dis",
            5712 => "t_5712_plej_aeldr_dis",
        ];
        return $names[$code];
    }

    /**
     * @param string $uploadTable
     * @param bool $delete
     * @return array<mixed>
     * @throws PhpfastcacheInvalidArgumentException
     */
    private function schema(string $uploadTable, bool $delete = false): array
    {
        $response = [];
        $table = new Table($uploadTable);
        $firstRow = $table->getFirstRecord();
        if (!$firstRow["success"]) {
            $response["success"] = false;
            $response["message"] = "Kunne ikke læse tabel";
            return $response;
        }
        if (empty($firstRow["data"]) || empty($firstRow["data"]["temakode"])) {
            $response["success"] = false;
            $response["message"] = "Kunne ikke læse temakode";
            return $response;
        }

        $themeCode = $firstRow["data"]["temakode"];
        $themeName = $this->getThemeName($themeCode);
        $uploadSchema = $table->getTableStructure()["data"];

        $fkgSchema = $this->fkgSchema($themeCode);
        $arr1 = [];
        $arr2 = [];
        $arr3 = [];
        $sqlInsertIds = [];
        $sqlUpdateIds = [];
        $checkFields = false;
        $missingField = "";

        $response["data"]["upload_schema"] = $uploadSchema;
        $response["data"]["fkg_schema"][] = $fkgSchema;

        foreach ($fkgSchema as $key => $value) {
            $check = false;
            foreach ($uploadSchema as $sValue) {
                if ($value[0] == $sValue["id"] || $key == $sValue["id"]) {
                    if ($key == $sValue["id"]) {
                        $fieldName = $key;
                    } else {
                        $fieldName = $value[0];
                    }
                    $response["data"]["fields"][$fieldName] = true;
                    if ($key != "objekt_id") {
                        if (strpos($key, 'saeson_s') !== false) {
                            $arr2[] = "('0001-'||" . $fieldName . ")::" . $value[2];
                        } else {
                            $arr2[] = $fieldName . "::" . $value[2];

                        }
                        $arr1[] = $key;
                    }
                    if (strpos($key, 'saeson_s') !== false) {
                        $arr3[] = "{$key}=('0001-'||{$uploadTable}.{$fieldName})::{$value[2]}";
                    } else {
                        $arr3[] = "{$key}={$uploadTable}.{$fieldName}::{$value[2]}";
                    }
                    $check = true;
                    break;
                }
            }
            if (!$check) {
                $response["data"]["fields"][$key] = false;
                if ($value[1]) {
                    $checkFields = true;
                    $missingField = $key;
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

        $split = explode(".", $uploadTable);
        $rowCount = $this->model->countRows($split[0], $split[1])["data"];


        $userFilter = new UserFilter("fkg", Session::getUser(), "*", "*", "*", "*", "fkg." . $themeName);
        $geofence = new GeofenceModel($userFilter);
        $rule = $geofence->authorize();

        $sql = "SELECT * FROM {$uploadTable} as t WHERE ST_intersects(t.the_geom, ST_transform(({$rule["filters"]["write_spatial"]}), 25832));";
        $res = $this->model->prepare($sql);
        try {
            $res->execute();
        } catch (PDOException $e) {
            $response['success'] = false;
            $response['message'] = $e->getMessage();
            $response['code'] = 401;
            return $response;
        }
        $countAfterRules = 0;
        while ($row = $this->model->fetchRow($res)) {
            if (isset($row["objekt_id"]) && $row["objekt_id"]) {
                $sqlUpdateIds[] = $row["objekt_id"];
            } else {
                $sqlInsertIds[] = $row["gid"];
            }
            $countAfterRules++;
        }

        $sqlExists = "SELECT 1 FROM fkg." . $themeName . " WHERE objekt_id=:objekt_id";
        $sqlUpdate = "UPDATE fkg." . $themeName . " SET " . implode(", ", $arr3) . " FROM " . $uploadTable . " WHERE fkg." . $themeName . ".objekt_id=" . $uploadTable . ".objekt_id::uuid AND " . $themeName . ".objekt_id=:objekt_id;";
        //echo $sqlUpdate . "\n\n";
        $resExists = $this->model->prepare($sqlExists);
        $resUpdate = $this->model->prepare($sqlUpdate);

        $response["data"]["updated_ids"] = [];
        foreach ($sqlUpdateIds as $objekt_id) {

            try {
                $resExists->execute(["objekt_id" => $objekt_id]);
            } catch (PDOException $e) {
                $response['success'] = false;
                $response['message'][] = $e->getMessage();
                $response['code'] = 401;
                return $response;
            }
            $rowExists = $this->model->fetchRow($resExists);
            if (!empty($rowExists)) {
                try {
                    $resUpdate->execute(["objekt_id" => $objekt_id]);
                } catch (PDOException $e) {
                    $response['success'] = false;
                    $response['message'][] = $e->getMessage();
                    $response['code'] = 401;
                    return $response;
                }
                $row = $this->model->fetchRow($resUpdate);
                $response["data"]["updated_ids"][] = $objekt_id;
            }
        }

        $sqlInsert = "INSERT INTO fkg." . $themeName . " (" . implode(",", $arr1) . ") (SELECT " . implode(",", $arr2) . " FROM " . $uploadTable . " WHERE gid=:gid) RETURNING objekt_id";
        //echo $sqlInsert . "\n\n";
        $resInsert = $this->model->prepare($sqlInsert);

        $response["data"]["inserted_ids"] = [];
        foreach ($sqlInsertIds as $gid) {
            try {
                $resInsert->execute(["gid" => $gid]);
            } catch (PDOException $e) {
                $response['success'] = false;
                $response['message'][] = explode("\n", $e->getMessage())[0];
                $response['message'][] = explode("\n", $e->getMessage())[1];
                //$response['message'][] = $e->getMessage();
                $response['code'] = 401;
                return $response;
            }

            $row = $this->model->fetchRow($resInsert);
            $response["data"]["inserted_ids"][] = $row["objekt_id"];
        }

        // Delete not effected rows
        $sqlDelete = "";
        $deleteCount = 0;
        $cvrKode = Session::get()["properties"]->cvr_kode;
        if (empty($cvrKode)) {
            $response['success'] = false;
            $response['message'] = "Der er ingen cvr kode tilknyttet brugeren. Kontakt supporten.";
            $response['code'] = 401;
            return $response;
        }
        if ($delete) {
            $deleteIds = array_merge($response["data"]["inserted_ids"], $response["data"]["updated_ids"]);
            if (sizeof($deleteIds) == 0) {
                $sqlDelete = "DELETE FROM fkg." . $themeName . " WHERE cvr_kode=?";
            } else {
                $sqlDelete = "DELETE FROM fkg." . $themeName . " WHERE cvr_kode=? AND objekt_id NOT IN (" . trim(str_repeat(', ?', count($deleteIds)), ', ') . ")";
            }
            $resDelete = $this->model->prepare($sqlDelete);
            try {
                $resDelete->execute(array_merge([$cvrKode], $deleteIds));
                $deleteCount = $resDelete->rowCount();
            } catch (PDOException $e) {
                $response['success'] = false;
                $response['message'][] = $e->getMessage();
                $response['code'] = 401;
                return $response;
            }
        }

        $this->model->commit();

        $response["success"] = true;
        $response["theme_name"] = $themeName;
        $response["delete_sql"] = $sqlDelete;
        $response["insert_count"] = sizeof($response["data"]["inserted_ids"]);
        $response["update_count"] = sizeof($response["data"]["updated_ids"]);
        $response["delete_count"] = $deleteCount;
        $response["skip_count"] = $rowCount - $countAfterRules;
        return $response;
    }

    /**
     * @return array<mixed>
     * @throws PhpfastcacheInvalidArgumentException
     */
    public function post_index(): array
    {
        $request = json_decode(Input::getBody(), true);
        $fileName = $request["fileName"];
        $delete = $request["delete"];
        $response = [];
        $dir = App::$param['path'] . "app/tmp/" . Connection::$param["postgisdb"] . "/__vectors";
        $safeName = Session::getUser() . "_" . md5(microtime() . rand());

        if (is_numeric($safeName[0])) {
            $safeName = "_" . $safeName;
        }

        // Check if file is .zips
        $zipCheck1 = explode(".", $fileName);
        $zipCheck2 = array_reverse($zipCheck1);
        if (strtolower($zipCheck2[0]) == "zip") {
            $ext = array("shp", "tab", "geojson", "gml", "kml", "mif", "gdb", "csv", "gpkg");
            $folderArr = array();
            $safeNameArr = array();
            for ($i = 0; $i < sizeof($zipCheck1) - 1; $i++) {
                $folderArr[] = $zipCheck1[$i];
            }
            $folder = implode(".", $folderArr);

            // ZIP start
            if (strtolower($zipCheck2[0]) == "zip") {
                $zip = new ZipArchive;
                $res = $zip->open($dir . "/" . $fileName);
                if ($res !== true) {
                    $response['success'] = false;
                    $response['message'] = $res;
                    return $response;
                }
                $zip->extractTo($dir . "/" . $folder);
                $zip->close();
            }

            if ($handle = opendir($dir . "/" . $folder)) {
                while (false !== ($entry = readdir($handle))) {
                    if ($entry !== "." && $entry !== "..") {
                        $zipCheck1 = explode(".", $entry);
                        $zipCheck2 = array_reverse($zipCheck1);
                        if (in_array(strtolower($zipCheck2[0]), $ext)) {
                            $fileName = $folder . "/" . $entry;
                            for ($i = 0; $i < sizeof($zipCheck1) - 1; $i++) {
                                $safeNameArr[] = $zipCheck1[$i];
                            }
                            $safeName = Model::toAscii(implode(".", $safeNameArr), array(), "_");
                            break;
                        }
                        $fileName = $folder;
                    }
                }
            }
        }

        $connectionStr = "\"PG:host=" . Connection::$param["postgishost"] . " user=" . Connection::$param["postgisuser"] . " password=" . Connection::$param["postgispw"] . " dbname=" . Connection::$param["postgisdb"] . "\"";

        $cmd = "ogr2postgis" .
            " -c {$connectionStr}" .
            " -t EPSG:25832" .
            " -o public" .
            " -n {$safeName}" .
            " -i" .
            " -p" .
            " '" . $dir . "/" . $fileName . "'";

        exec($cmd . ' > /dev/null', $out, $err);

        // Check ogr2ogr output
        if ($out[0] == "") {
            // Bust cache, in case of layer already exist
            Tilecache::bust(Connection::$param["postgisschema"] . "." . $safeName);
        } else {
            $response['success'] = false;
            $response['message'] = Session::createLog($out, $fileName);
            $response['out'] = $out;
            Session::createLog($out, $fileName);

            // Make sure the table is dropped if not
            // skipping failures and it didn't exists before
            // =================================================
            $sql = "DROP TABLE " . Connection::$param["postgisschema"] . "." . $safeName;
            $res = $this->model->prepare($sql);
            try {
                $res->execute();
            } catch (PDOException $e) {
            }
            return $response;
        }

        //$response['cmd'] = $cmd;

        $s = $this->schema("public." . $safeName, $delete);

        if (!$s["success"]) {
            $response['success'] = false;
            $response['message'] = $s["message"];
            $response['file_name'] = $fileName;
            $response['code'] = "403";
        } else {
            $response['message'] = "Data indlæst";
            $response['success'] = true;
        }
        $response["fkg_report"] = $s["data"];
        $response["theme_name"] = $s["theme_name"];
        $response["delete_sql"] = $s["delete_sql"];
        $response["delete_count"] = $s["delete_count"];
        $response["insert_count"] = $s["insert_count"];
        $response["update_count"] = $s["update_count"];
        $response["skip_count"] = $s["skip_count"];
        $response["session"] = Session::get()["properties"]->cvr_kode;
        return $response;
    }
}
