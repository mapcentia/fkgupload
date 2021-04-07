<?php
/**
 * @author     Martin Høgh <mh@mapcentia.com>
 * @copyright  2013-2021 MapCentia ApS
 * @license    http://www.gnu.org/licenses/#AGPL  GNU AFFERO GENERAL PUBLIC LICENSE 3
 *
 */

namespace app\extensions\fkgupload\api;


/**
 * Class Schemata
 * @package app\extensions\fkgupload\api
 */
class Schemata
{
    /**
     * @var array[][]
     */
    static public $schemata = [
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
            "adr_id" => ["adr_id", false, "uuid"],

            "foto_link" => ["foto_link", false, "varchar"],
            "foto_link1" => ["foto_link1", false, "varchar"],
            "foto_link2" => ["foto_link2", false, "varchar"],
            "foto_link3" => ["foto_link3", false, "varchar"],

            "folder_k" => ["folder_k", false, "int"],
            "folde_link" => ["folde_link", false, "varchar"],
            "foldelink1" => ["foldelink1", false, "varchar"],
            "foldelink2" => ["foldelink2", false, "varchar"],
            "foldelink3" => ["foldelink3", false, "varchar"],

            "filmlink" => ["filmlink", false, "varchar"],
            "filmlink1" => ["filmlink1", false, "varchar"],
            "filmlink2" => ["filmlink3", false, "varchar"],
            "filmlink3" => ["filmlink3", false, "varchar"],

            "geofafoto" => ["geofafoto", false, "varchar"],
            "geofafoto1" => ["geofafoto1", false, "varchar"],
            "geofafoto2" => ["geofafoto2", false, "varchar"],
            "geofafoto3" => ["geofafoto3", false, "varchar"],

            "link" => ["link", false, "varchar"],
            "link1" => ["link1", false, "varchar"],
            "link2" => ["link2", false, "varchar"],
            "link3" => ["ink3", false, "varchar"],

            "ansva_v_k" => ["ansva_v_k", false, "int"], // Felt defineret under: 4.1

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
            "adr_id" => ["adr_id", false, "uuid"],

            "foto_link" => ["foto_link", false, "varchar"],
            "foto_link1" => ["foto_link1", false, "varchar"],
            "foto_link2" => ["foto_link2", false, "varchar"],
            "foto_link3" => ["foto_link3", false, "varchar"],

            "folder_k" => ["folder_k", false, "int"],
            "folde_link" => ["folde_link", false, "varchar"],
            "foldelink1" => ["foldelink1", false, "varchar"],
            "foldelink2" => ["foldelink2", false, "varchar"],
            "foldelink3" => ["foldelink3", false, "varchar"],

            "filmlink" => ["filmlink", false, "varchar"],
            "filmlink1" => ["filmlink1", false, "varchar"],
            "filmlink2" => ["filmlink2", false, "varchar"],
            "filmlink3" => ["filmlink3", false, "varchar"],

            "geofafoto" => ["geofafoto", false, "varchar"],
            "geofafoto1" => ["geofafoto1", false, "varchar"],
            "geofafoto2" => ["geofafoto2", false, "varchar"],
            "geofafoto3" => ["geofafoto3", false, "varchar"],

            "link" => ["link", false, "varchar"],
            "link1" => ["link1", false, "varchar"],
            "link2" => ["link2", false, "varchar"],
            "link3" => ["ink3", false, "varchar"],

            "ansva_v_k" => ["ansva_v_k", false, "int"], // Felt defineret under: 4.1

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
            "gpx_link" => ["gpx_link", false, "varchar"],
            "adr_id" => ["adr_id", false, "uuid"],

            "foto_link" => ["foto_link", false, "varchar"],
            "foto_link1" => ["foto_link1", false, "varchar"],
            "foto_link2" => ["foto_link2", false, "varchar"],
            "foto_link3" => ["foto_link3", false, "varchar"],

            "folder_k" => ["folder_k", false, "int"],
            "folde_link" => ["folde_link", false, "varchar"],
            "foldelink1" => ["foldelink1", false, "varchar"],
            "foldelink2" => ["foldelink2", false, "varchar"],
            "foldelink3" => ["foldelink3", false, "varchar"],

            "filmlink" => ["filmlink", false, "varchar"],
            "filmlink1" => ["filmlink1", false, "varchar"],
            "filmlink2" => ["filmlink2", false, "varchar"],
            "filmlink3" => ["filmlink3", false, "varchar"],

            "geofafoto" => ["geofafoto", false, "varchar"],
            "geofafoto1" => ["geofafoto1", false, "varchar"],
            "geofafoto2" => ["geofafoto2", false, "varchar"],
            "geofafoto3" => ["geofafoto3", false, "varchar"],

            "link" => ["link", false, "varchar"],
            "link1" => ["link1", false, "varchar"],
            "link2" => ["link2", false, "varchar"],
            "link3" => ["ink3", false, "varchar"],

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
}