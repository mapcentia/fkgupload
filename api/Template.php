<?php
/**
 * @author     Martin Høgh <mh@mapcentia.com>
 * @copyright  2013-2021 MapCentia ApS
 * @license    http://www.gnu.org/licenses/#AGPL  GNU AFFERO GENERAL PUBLIC LICENSE 3
 *
 */

namespace app\extensions\fkgupload\api;

use app\inc\Controller;
use app\inc\Route;
use app\models\Database;
use app\models\Sql;
use app\inc\Session;
use Phpfastcache\Exceptions\PhpfastcacheInvalidArgumentException;


/**
 * Class Template
 * @package app\extensions\fkgupload\api
 */
class Template extends Controller
{

    /**
     * Template constructor.
     */
    function __construct()
    {
        parent::__construct();
        Session::start();
        Session::authenticate(null);
        Database::setDb(Session::getDatabase());
    }

    /**
     * @return array<mixed>|null
     * @throws PhpfastcacheInvalidArgumentException
     */
    public function get_index(): ?array
    {
        $format = urldecode(Route::getParam("format"));
        $layer = Route::getParam("layer");
        $layerNum = (int)filter_var($layer, FILTER_SANITIZE_NUMBER_INT);
        $cvrKode = Session::get()["properties"]->cvr_kode;

        $fieldArr = ["temakode"];
        $schema = Schemata::$schemata[$layerNum];
        foreach ($schema as $key => $value) {
            if ($format == "ESRI Shapefile") {
                $fieldArr[] = "{$key} AS {$value[0]}";
            } else {
                $fieldArr[] = $key;
            }
        }
        $limit = $format == "MapInfo File" ? 0 : 1000000;
        $fieldStr = implode(",", $fieldArr);
        $q = "select {$fieldStr} from fkg.{$layer} WHERE cvr_kode = {$cvrKode} limit {$limit}";
        $sql = new Sql("25832");
        $res = $sql->sql($q, "UTF8", "ogr/" . $format, null, false, null, $schema["geometri"][3], $layer);
        if (!$res["success"]) {
            return $res;
        }
        return null;
    }
}
