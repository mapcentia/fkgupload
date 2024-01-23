<?php
/**
 * @author     Martin Høgh <mh@mapcentia.com>
 * @copyright  2013-2024 MapCentia ApS
 * @license    http://www.gnu.org/licenses/#AGPL  GNU AFFERO GENERAL PUBLIC LICENSE 3
 *
 */

namespace app\extensions\fkgupload\api;

use app\inc\Controller;
use app\inc\Route;
use app\models\Database;
use app\models\Sql;
use Exception;
use Phpfastcache\Exceptions\PhpfastcacheInvalidArgumentException;
use PhpOffice\PhpSpreadsheet\Writer\Exception as WriterException;


/**
 * Class Gpx
 * @package app\extensions\fkgupload\api
 */
class Gpx extends Controller
{
    const LLAYER = "fkg.t_5802_fac_li";
    const PLAYER = "fkg.t_5800_fac_pkt";

    /**
     * Template constructor.
     */
    function __construct()
    {
        parent::__construct();
        Database::setDb("fkg");
    }

    /**
     * @return array|null
     * @throws Exception|WriterException|PhpfastcacheInvalidArgumentException
     */
    public function get_index(): ?array
    {
        $objekt_id = urldecode(Route::getParam("objekt_id"));
        $type = Route::getParam("type");

        if ($type != "point" && $type != "line") {
            return [
                "success" => false,
                "code" => 400,
                "message" => "Type skal være 'point' eller 'line'",
            ];
        }

        $q = "select navn as name,geometri from " . ($type == "point" ? self::PLAYER : self::LLAYER) . " WHERE objekt_id='$objekt_id'";
        $sql = new Sql("4326");
        $res = $sql->sql($q, "UTF8", "ogr/GPX", null, false, null, ($type == "point" ? "POINT" : "LINESTRING"), "gpx_" . $objekt_id);
        if (!$res["success"]) {
            return $res;
        }
        return null;
    }
}
