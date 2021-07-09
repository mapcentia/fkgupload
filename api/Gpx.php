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
 * Class Gpx
 * @package app\extensions\fkgupload\api
 */
class Gpx extends Controller
{
    const LAYER = "fkg.t_5802_fac_li";

    /**
     * Template constructor.
     */
    function __construct()
    {
        parent::__construct();
        Database::setDb("fkg");
    }

    /**
     * @return array<mixed>|null
     * @throws PhpfastcacheInvalidArgumentException
     */
    public function get_index(): ?array
    {
        $objekt_id = urldecode(Route::getParam("objekt_id"));

        $q = "select navn as name,geometri from " . self::LAYER . " WHERE objekt_id='{$objekt_id}'";
        $sql = new Sql("4326");
        $res = $sql->sql($q, "UTF8", "ogr/GPX", null, false, null, "MULTILINESTRING", "gpx_" . $objekt_id);
        if (!$res["success"]) {
            return $res;
        }
        return null;
    }
}
