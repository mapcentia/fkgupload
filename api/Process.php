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
use app\inc\Route;
use app\models\Database;
use app\conf\Connection;
use app\inc\Session;
use app\inc\Model;
use app\inc\UserFilter;
use app\models\Geofence as GeofenceModel;
use app\models\Rule;
use app\models\Table;
use Aws\S3\S3Client;
use League\Flysystem\AwsS3V3\AwsS3V3Adapter;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemException;
use PDOException;
use Phpfastcache\Exceptions\PhpfastcacheInvalidArgumentException;
use ZipArchive;

const S3_FOLDER = "fkg";


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
        return Schemata::$schemata[$schema];
    }

    /**
     * @param int $code
     * @return string
     */
    private function getThemeName(int $code): string
    {
        $names = [
            5003 => "t_5003_draenledn",
            5004 => "t_5004_draenomr",
            5011 => "t_5011_draenbroend",
            5300 => "t_5300_genopd_ret",
            5600 => "t_5600_vintervedl",
            5601 => "t_5601_hasti_daemp",
            5602 => "t_5602_p_zoner",
            5603 => "t_5603_hasti_omraade",
            5604 => "t_5604_koer_begr",
            5605 => "t_5605_vejbyggel",
            5606 => "t_5606_vejinv",
            5608 => "t_5608_cykelknudepunkter",
            5609 => "t_5609_cykelknudepunktsstraekninger",
            5610 => "t_5610_cykelplanlaegning",
            5611 => "t_5611_vinterute",
            5612 => "t_5612_vinterserviceomraade",
            5613 => "t_5613_p_plads",
            5614 => "t_5614_stationering_cykelplanlaegning",

            5700 => "t_5700_grundej",
            5701 => "t_5701_lok_omr",
            5702 => "t_5702_skorst_fej",
            5705 => "t_5705_forp_are",
            5706 => "t_5706_havn_are",
            5707 => "t_5707_grunds",
            5710 => "t_5710_born_skole_dis",
            5711 => "t_5711_and_dis",
            5712 => "t_5712_plej_aeldr_dis",
            5713 => "t_5713_prog_stat_dis",
            5715 => "t_5715_botilbud",
            5716 => "t_5716_servicetilbud",
            5717 => "t_5717_hoeringspart",

            5800 => "t_5800_fac_pkt",
            5801 => "t_5801_fac_fl",
            5802 => "t_5802_fac_li",
            5607 => "t_5607_ladefacilitet",

            6800 => "t_6800_parl_fl",
            6801 => "t_6801_parl_li",
            6802 => "t_6802_parl_pkt",
            6803 => "t_6803_parl_omr",
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
                        if (str_contains($key, 'saeson_s')) {
                            $arr2[] = "('0001-'||" . $fieldName . ")::" . $value[2];
                        } else {
                            $arr2[] = $fieldName . "::" . $value[2];

                        }
                        $arr1[] = $key;
                    }
                    if (str_contains($key, 'saeson_s')) {
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

        $Rule = new Rule();
        $userFilter = new UserFilter(Session::getUser(), "*", "insert", "*", "fkg", "*");
        $geofence = new GeofenceModel($userFilter);
        $rule = $geofence->authorize($Rule->get());

        if (empty($rule["access"]) || $rule["access"] == "deny") {
            $response['success'] = false;
            $response['message'] = "Ingen adgang til datasættet";
            $response['code'] = 401;
            return $response;
        }
        if ($rule["access"] == "limit") {
            // Prepare filter to be use on oploaded data
            $filter = str_replace("geometri", "the_geom", $rule["filters"]["filter"]);
            $sql = "SELECT * FROM $uploadTable WHERE ($filter);";
        } else {
            $sql = "SELECT * FROM $uploadTable";
        }
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
                $this->model->fetchRow($resUpdate); // TODO can this line be deleted?
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

        $fileFullPath = $dir . "/" . $fileName;

        // Check if file is .zips
        $zipCheck1 = explode(".", $fileName);
        $zipCheck2 = array_reverse($zipCheck1);

        // Check if file is image
        if (strtolower($zipCheck2[0]) == "png" || strtolower($zipCheck2[0]) == "jpg" || strtolower($zipCheck2[0]) == "jpeg") {
            try {
                $fotoObjektId = $this->insertInto7901();
            } catch (PDOException $e) {
                $response['success'] = false;
                $response['message'] = $e->getMessage();
                $response['code'] = 401;
                return $response;
            }
            return $this->storeImageInS3($fileName, $fotoObjektId);
        } else {
            if (strtolower($zipCheck2[0]) == "zip") {
                $zip = new ZipArchive;
                $res = $zip->open($dir . "/" . $fileName);
                if ($res !== true) {
                    $response['success'] = false;
                    $response['message'] = $res;
                    return $response;
                }
                $zip->extractTo($dir . "/" . $safeName);
                $zip->close();
                $fileFullPath = $dir . "/" . $safeName;
            }

            $connectionStr = "\"PG:host=" . Connection::$param["postgishost"] . " user=" . Connection::$param["postgisuser"] . " password=" . Connection::$param["postgispw"] . " dbname=" . Connection::$param["postgisdb"] . "\"";

            $cmd = "ogr2postgis" .
                " -c {$connectionStr}" .
                " -s EPSG:25832" .
                " -t EPSG:25832" .
                " -o public" .
                " -a " . // Need to append if folder from zip file
                " -n {$safeName}" .
                " -i" .
                " -p" .
                " '" . $fileFullPath . "'";

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
            $response["cmd"] = $cmd;
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

    /**
     * @return string
     * @throw PDOException
     */
    private function insertInto7901(): string
    {

        $cvrKode = Session::get()["properties"]->cvr_kode;
        $brugerId = Session::get()["screen_name"];
        $sql = "INSERT INTO fkg.t_7901_foto(cvr_kode, bruger_id, oprindkode, statuskode, off_kode) VALUES (:cvrKode,:brugerId,0,3,1) RETURNING objekt_id";
        $res = $this->model->prepare($sql);
        $res->execute(["cvrKode" => $cvrKode, "brugerId" => $brugerId]);
        $row = $this->model->fetchRow($res);
        return $row["objekt_id"];

    }

    /**
     * @param string $fileName
     * @param string $fotoObjekId
     * @return array<mixed>
     * @throws FilesystemException
     */
    public function storeImageInS3(string $fileName, string $fotoObjekId): array
    {

        @set_time_limit(5 * 60);
        $mainDir = App::$param['path'] . "/app/tmp/fkg";
        $targetDir = $mainDir . "/__vectors";

        $thumbNailsSizes = [171, 360, 560, 1600];

        if (!file_exists($mainDir)) {
            @mkdir($mainDir);
        }
        if (!file_exists($targetDir)) {
            @mkdir($targetDir);
        }

        foreach ($thumbNailsSizes as $size) {
            if (!file_exists($targetDir . "/" . (string)$size)) {
                @mkdir($targetDir . "/" . (string)$size);
            }
        }

        $client = new S3Client([
            'credentials' => [
                'key' => App::$param["s3"]["id"],
                'secret' => App::$param["s3"]["secret"],
            ],
            'region' => 'eu-west-1',
            'version' => 'latest',
        ]);

        $objektIdName = $fotoObjekId . ".jpg";

        $adapter = new AwsS3V3Adapter($client, 'mapcentia-www');
        $filesystem = new Filesystem($adapter);
        $filePath = $targetDir . DIRECTORY_SEPARATOR . $fileName;
        $filesystem->write(S3_FOLDER . DIRECTORY_SEPARATOR . $objektIdName, file_get_contents($filePath));
        foreach ($thumbNailsSizes as $size) {
            $this->createThumbnail($fileName, $size, $size, $targetDir, $targetDir . DIRECTORY_SEPARATOR . (string)$size . DIRECTORY_SEPARATOR);
            $filesystem->write(S3_FOLDER . DIRECTORY_SEPARATOR . (string)$size . DIRECTORY_SEPARATOR . $objektIdName, file_get_contents($targetDir . DIRECTORY_SEPARATOR . (string)$size . DIRECTORY_SEPARATOR . $fileName));
        }

        return [
            "success" => true,
            "image" => $objektIdName,
        ];
    }

    /**
     * @param string $image_name
     * @param int $new_width
     * @param int $new_height
     * @param string $uploadDir
     * @param string $moveToDir
     * @return bool
     */
    public function createThumbnail(string $image_name, int $new_width, int $new_height, string $uploadDir, string $moveToDir): bool
    {
        $result = false;
        $src_img = false;
        $thumb_h = 0;
        $thumb_w = 0;

        $path = $uploadDir . '/' . $image_name;

        $mime = getimagesize($path);

        if ($mime['mime'] == 'image/png') {
            $src_img = imagecreatefrompng($path);
        }
        if ($mime['mime'] == 'image/jpg' || $mime['mime'] == 'image/jpeg' || $mime['mime'] == 'image/pjpeg') {
            $src_img = imagecreatefromjpeg($path);
        }

        $exif = exif_read_data($path);

        if (isset($exif['Orientation'])) {
            $orientation = $exif['Orientation'];
        } elseif (isset($exif['IFD0']['Orientation'])) {
            $orientation = $exif['IFD0']['Orientation'];
        } else {
            $orientation = 0;
        }

        switch ($orientation) {
            case 3: // rotate 180 degrees
                $src_img = imagerotate($src_img, 180, 0);
                break;

            case 6: // rotate 90 degrees CW
                $src_img = imagerotate($src_img, 270, 0);
                break;

            case 8: // rotate 90 degrees CCW
                $src_img = imagerotate($src_img, 90, 0);
                break;
        }

        $old_x = imageSX($src_img);
        $old_y = imageSY($src_img);

        if ($old_x > $old_y) {
            $thumb_w = $new_width;
            $thumb_h = $old_y * ($new_height / $old_x);
        }

        if ($old_x < $old_y) {
            $thumb_w = $old_x * ($new_width / $old_y);
            $thumb_h = $new_height;
        }

        if ($old_x == $old_y) {
            $thumb_w = $new_width;
            $thumb_h = $new_height;
        }

        $dst_img = ImageCreateTrueColor($thumb_w, (int)$thumb_h);

        imagecopyresampled($dst_img, $src_img, 0, 0, 0, 0, $thumb_w, (int)$thumb_h, $old_x, $old_y);

        // New save location
        $new_thumb_loc = $moveToDir . $image_name;

        if ($mime['mime'] == 'image/png') {
            $result = imagepng($dst_img, $new_thumb_loc, 8);
        }
        if ($mime['mime'] == 'image/jpg' || $mime['mime'] == 'image/jpeg' || $mime['mime'] == 'image/pjpeg') {
            $result = imagejpeg($dst_img, $new_thumb_loc, 80);
        }

        imagedestroy($dst_img);
        imagedestroy($src_img);

        return $result;
    }

    /**
     * @return array<mixed>
     */
    public function post_7900(): array
    {
        $request = json_decode(Input::getBody(), true);

        $cvrKode = Session::get()["properties"]->cvr_kode;
        $brugerId = Session::get()["screen_name"];
        $tema = $request["tema"];
        $foto_objek = $request["foto_objek"];
        $foto_lokat = $request["foto_lokat"];

        $sql = "INSERT INTO fkg.t_7900_fotoforbindelse(cvr_kode, bruger_id, oprindkode, statuskode, off_kode, fkg_tema, foto_objek, foto_lokat, primaer_kode, tilgaeng_kode)
                VALUES (:cvrKode, :brugerId, 0, 3, 1, :tema, :foto_objek, :foto_lokat, 0, 0)";

        $res = $this->model->prepare($sql);
        try {
            $res->execute(["cvrKode" => $cvrKode, "brugerId" => $brugerId, "tema" => $tema, "foto_objek" => $foto_objek, "foto_lokat" => $foto_lokat]);
        } catch (PDOException $e) {
            $response['success'] = false;
            $response['message'][] = $e->getMessage();
            $response['code'] = 401;
            return $response;
        }
        $response['success'] = true;
        return $response;

    }

    /**
     * @return array<mixed>
     */
    public function delete_7900(): array
    {
        $objekt_id = Route::getParam("objekt_id");
        $sql = "DELETE FROM fkg.t_7900_fotoforbindelse WHERE objekt_id=:objekt_id";
        $res = $this->model->prepare($sql);
        try {
            $res->execute(["objekt_id" => $objekt_id]);
        } catch (PDOException $e) {
            $response['success'] = false;
            $response['message'][] = $e->getMessage();
            $response['code'] = 401;
            return $response;
        }
        $response['success'] = true;
        return $response;
    }

    /**
     * Returner alle foto id'er for cvr nr, der IKKE allerede er tilknyttet den angivne facilitet (objekt_id)
     * @return array<mixed>
     */
    public function get_7901(): array
    {
        $objekt_id = Route::getParam("objekt_id");
        $cvrKode = Session::get()["properties"]->cvr_kode;
        $sql = "select * from fkg.t_7901_foto where cvr_kode=:cvrKode and objekt_id not in (select foto_lokat from fkg.t_7900_fotoforbindelse where foto_objek=:objekt_id) order by oprettet desc limit 100";
        $res = $this->model->prepare($sql);
        try {
            $res->execute(["cvrKode" => $cvrKode, "objekt_id" => $objekt_id]);
        } catch (PDOException $e) {
            $response['success'] = false;
            $response['message'][] = $e->getMessage();
            $response['code'] = 401;
            return $response;
        }
        while ($row = $this->model->fetchRow($res)) {
            $response['data'][] = $row["objekt_id"];
        }
        $response['success'] = true;
        return $response;
    }

    /**
     * Returnerer alle foto id'er (foto_objek) for den angivne facilitet (objekt_id)
     * @return array<mixed>
     */
    public function get_7900(): array
    {
        $objekt_id = Route::getParam("objekt_id");
        $sql = "select * from fkg.t_7900_fotoforbindelse where foto_objek=:objekt_id order by oprettet desc,objekt_id";
        $res = $this->model->prepare($sql);
        try {
            $res->execute(["objekt_id" => $objekt_id]);
        } catch (PDOException $e) {
            $response['success'] = false;
            $response['message'][] = $e->getMessage();
            $response['code'] = 401;
            return $response;
        }
        while ($row = $this->model->fetchRow($res)) {
            $response['data'][] = [$row["foto_lokat"], $row["primaer_kode"], $row["objekt_id"]];
        }
        $response['success'] = true;
        return $response;
    }

    /**
     * @return array<mixed>
     */
    public function put_7900(): array
    {
        $request = json_decode(Input::getBody(), true);
        $objekt_id = $request["objekt_id"];
        $objekt_id_7900 = $request["objekt_id_7900"];

        $sql = "update fkg.t_7900_fotoforbindelse set primaer_kode=0 where foto_objek=:objekt_id;";
        $res = $this->model->prepare($sql);
        try {
            $res->execute(["objekt_id" => $objekt_id]);
        } catch (PDOException $e) {
            $response['success'] = false;
            $response['message'][] = $e->getMessage();
            $response['code'] = 401;
            return $response;
        }

        $sql = "update fkg.t_7900_fotoforbindelse set primaer_kode=1 where objekt_id=:objekt_id;";
        $res = $this->model->prepare($sql);
        try {
            $res->execute(["objekt_id" => $objekt_id_7900]);
        } catch (PDOException $e) {
            $response['success'] = false;
            $response['message'][] = $e->getMessage();
            $response['code'] = 401;
            return $response;
        }
        $response['success'] = true;
        return $response;
    }
}
