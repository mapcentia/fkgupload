<?php
/**
 * @author     Martin Høgh <mh@mapcentia.com>
 * @copyright  2013-2024 MapCentia ApS
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
    const MULTIPOINT = "MULTIPOINT";
    const MULTILINESTRING = "MULTILINESTRING";
    const MULTIPOLYGON = "MULTIPOLYGON";

    /**
     * @var array[][]
     */
    static public $schemata = [
        5700 => [
            "objekt_id" => ["objekt_id", false, "uuid"],
            "cvr_kode" => ["cvr_kode", true, "int"],
            "bruger_id" => ["bruger_id", true, "varchar"],
            "oprindkode" => ["oprindkode", true, "int"],
            "statuskode" => ["statuskode", true, "int"],
            "off_kode" => ["off_kode", true, "int"],

            "forening_type_kode" => ["foren_ty_k", true, "int"],
            "forening_navn" => ["foren_navn", true, "varchar"],
            "lokalpl_nr" => ["lokalpl_nr", false, "varchar"],
            "formand" => ["formand", false, "varchar"],
            "gf_tlf" => ["gf_tlf", false, "int"],
            "gf_mail" => ["gf_mail", false, "varchar"],
            "gf_adr_beskyt_kode" => ["gf_besky_k", false, "varchar"],
            "vedtaegt_kode" => ["vedtaegt_k", false, "int"],

            "noegle" => ["noegle", false, "varchar"],
            "link" => ["link", false, "varchar"],
            "note" => ["note", false, "varchar"],
            "cvr_opslag" => ["cvr_opslag", false, "varchar"],
            "geometri" => ["the_geom", true, "geometry", self::MULTIPOLYGON],
        ],
        5701 => [
            "objekt_id" => ["objekt_id", false, "uuid"],
            "cvr_kode" => ["cvr_kode", true, "int"],
            "bruger_id" => ["bruger_id", true, "varchar"],
            "oprindkode" => ["oprindkode", true, "int"],
            "statuskode" => ["statuskode", true, "int"],
            "off_kode" => ["off_kode", true, "int"],

            "lokaludv_type_kode" => ["Lokalu_t_k", true, "int"],
            "lokaludv_navn" => ["lokalu_n", true, "varchar"],

            "noegle" => ["noegle", false, "varchar"],
            "sagsnr" => ["sagsnr", false, "varchar"],
            "link" => ["link", false, "varchar"],
            "note" => ["note", false, "varchar"],
            "geometri" => ["the_geom", true, "geometry", self::MULTIPOLYGON],
        ],
        5702 => [
            "objekt_id" => ["objekt_id", false, "uuid"],
            "cvr_kode" => ["cvr_kode", true, "int"],
            "bruger_id" => ["bruger_id", true, "varchar"],
            "oprindkode" => ["oprindkode", true, "int"],
            "statuskode" => ["statuskode", true, "int"],
            "off_kode" => ["off_kode", true, "int"],

            "skorstenf_distrikt_nr" => ["skor_di_nr", false, "int"],
            "skorstenf_distrikt_navn" => ["skor_di_na", false, "varchar"],
            "gyldig_fra" => ["gyldig_fra", true, "date"],
            "gyldig_til" => ["gyldig_til", false, "date"],
            "skorstensfejer_firma" => ["skor_firma", true, "varchar"],


            "noegle" => ["noegle", false, "varchar"],
            "sagsnr" => ["sagsnr", false, "varchar"],
            "link" => ["link", false, "varchar"],
            "note" => ["note", false, "varchar"],
            "cvr_opslag" => ["cvr_opslag", false, "varchar"],
            "geometri" => ["the_geom", true, "geometry", self::MULTIPOLYGON],
        ],
        5705 => [
            "objekt_id" => ["objekt_id", false, "uuid"],
            "cvr_kode" => ["cvr_kode", true, "int"],
            "bruger_id" => ["bruger_id", true, "varchar"],
            "oprindkode" => ["oprindkode", true, "int"],
            "statuskode" => ["statuskode", true, "int"],
            "off_kode" => ["off_kode", true, "int"],

            "forpagter_navn" => ["forpagt_na", true, "varchar"],
            "forpagter_formaal" => ["forpagt_fm", false, "varchar"],
            "udlejning_kode" => ["udlejnin_k", true, "int"],
            "landbrug_kode" => ["Landbrug_k", true, "int"],

            "noegle" => ["noegle", false, "varchar"],
            "sagsnr" => ["sagsnr", false, "varchar"],
            "link" => ["link", false, "varchar"],
            "note" => ["note", false, "varchar"],
            "cvr_opslag" => ["cvr_opslag", false, "varchar"],
            "geometri" => ["the_geom", true, "geometry", self::MULTIPOLYGON],
        ],
        5710 => [
            "objekt_id" => ["objekt_id", false, "uuid"],
            "cvr_kode" => ["cvr_kode", true, "int"],
            "bruger_id" => ["bruger_id", true, "varchar"],
            "oprindkode" => ["oprindkode", true, "int"],
            "statuskode" => ["statuskode", true, "int"],
            "off_kode" => ["off_kode", true, "int"],

            "udd_distrikt_nr" => ["udd_dis_nr", false, "int"],
            "udd_distrikt_navn" => ["udd_dis_na", false, "varchar"],
            "udd_distrikt_type_kode" => ["udd_d_ty_k", true, "int"],
            "starttrin_kode" => ["strtr_kode", false, "int"],
            "sluttrin_kode" => ["slutr_kode", false, "int"],

            "noegle" => ["noegle", false, "varchar"],
            "sagsnr" => ["sagsnr", false, "varchar"],
            "link" => ["link", false, "varchar"],
            "note" => ["note", false, "varchar"],
            "geometri" => ["the_geom", true, "geometry", self::MULTIPOLYGON],
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
            "note" => ["note", false, "varchar"],
            "geometri" => ["the_geom", true, "geometry", self::MULTIPOLYGON],
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
            "note" => ["note", false, "varchar"],
            "geometri" => ["the_geom", true, "geometry", self::MULTIPOLYGON],
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
            "note" => ["note", false, "varchar"],
            "geometri" => ["the_geom", true, "geometry", self::MULTIPOLYGON],
        ],
        5800 => [
            "objekt_id" => ["objekt_id", false, "uuid"],
            "cvr_kode" => ["cvr_kode", true, "int"],
            "bruger_id" => ["bruger_id", true, "varchar"],
            "oprindkode" => ["oprindkode", true, "int"],
            "statuskode" => ["statuskode", true, "int"],
            "off_kode" => ["off_kode", true, "int"],
            "noegle" => ["noegle", false, "varchar"],
            "note" => ["note", false, "varchar"],

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
            "link3" => ["link3", false, "varchar"],

            "ansva_v_k" => ["ansva_v_k", false, "int"], // Felt defineret under: 4.1

            "geometri" => ["the_geom", true, "geometry", self::MULTIPOINT],

            "saeson_bem" => ["saeson_bem", false, "varchar"],
            "vejkode" => ["vejkode", false, "int"],
            "vejnavn" => ["vejnavn", false, "varchar"],
            "cvf_vejkode" => ["cvf_vejkode", false, "varchar"],
            "husnr" => ["husnr", false, "varchar"],
            "postnr" => ["postnr", false, "int"],
            "kvalitet_k" => ["kvalitet_k", false, "int"],

            "tilgaeng_opl" => ["tilgaeng_o", false, "jsonb"],
            "tilgaeng_beskriv" => ["tilgaeng_b", false, "varchar"],
        ],
        5801 => [
            "objekt_id" => ["objekt_id", false, "uuid"],
            "cvr_kode" => ["cvr_kode", true, "int"],
            "bruger_id" => ["bruger_id", true, "varchar"],
            "oprindkode" => ["oprindkode", true, "int"],
            "statuskode" => ["statuskode", true, "int"],
            "off_kode" => ["off_kode", true, "int"],
            "noegle" => ["noegle", false, "varchar"],
            "note" => ["note", false, "varchar"],

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

            "geometri" => ["the_geom", true, "geometry", self::MULTIPOLYGON],

            "saeson_bem" => ["saeson_bem", false, "varchar"],
            "vejkode" => ["vejkode", false, "int"],
            "vejnavn" => ["vejnavn", false, "varchar"],
            "cvf_vejkode" => ["cvf_vejkode", false, "varchar"],
            "husnr" => ["husnr", false, "varchar"],
            "postnr" => ["postnr", false, "int"],
            "kvalitet_k" => ["kvalitet_k", false, "int"],
            "tilgaeng_opl" => ["tilgaeng_o", false, "jsonb"],
            "tilgaeng_beskriv" => ["tilgaeng_b", false, "varchar"],
        ],
        5802 => [
            "objekt_id" => ["objekt_id", false, "uuid"],
            "cvr_kode" => ["cvr_kode", true, "int"],
            "bruger_id" => ["bruger_id", true, "varchar"],
            "oprindkode" => ["oprindkode", true, "int"],
            "statuskode" => ["statuskode", true, "int"],
            "off_kode" => ["off_kode", true, "int"],
            "noegle" => ["noegle", false, "varchar"],
            "note" => ["note", false, "varchar"],

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

            "geometri" => ["the_geom", true, "geometry", self::MULTILINESTRING],

            "saeson_bem" => ["saeson_bem", false, "varchar"],
            "vejkode" => ["vejkode", false, "int"],
            "vejnavn" => ["vejnavn", false, "varchar"],
            "cvf_vejkode" => ["cvf_vejkode", false, "varchar"],
            "husnr" => ["husnr", false, "varchar"],
            "postnr" => ["postnr", false, "int"],
            "kvalitet_k" => ["kvalitet_k", false, "int"],
            "tilgaeng_opl" => ["tilgaeng_o", false, "jsonb"],
            "tilgaeng_beskriv" => ["tilgaeng_b", false, "varchar"],
        ],

        5607 => [
            "objekt_id" => ["objekt_id", false, "uuid"],
            "cvr_kode" => ["cvr_kode", true, "int"],
            "bruger_id" => ["bruger_id", true, "varchar"],
            "oprindkode" => ["oprindkode", true, "int"],
            "statuskode" => ["statuskode", true, "int"],
            "off_kode" => ["off_kode", true, "int"],
            "noegle" => ["noegle", false, "varchar"],
            "note" => ["note", false, "varchar"],

            "adr_id" => ["adr_id", false, "uuid"],
            "ansvar_org" => ["ansvar_org", false, "varchar"],
            "antal_ladepunkter" => ["ladepkt", false, "int"],
            "anvendelsesgrad_kwh" => ["anvgr_kWh", false, "int"],
            "driftstart_fra" => ["drift_fra", false, "date"],
            "foto_link" => ["link", false, "varchar"],
            "foto_link1" => ["link", false, "varchar"],
            "foto_link2" => ["link", false, "varchar"],
            "foto_link3" => ["link", false, "varchar"],
            "effekt_type_kode" => ["effekt_k", false, "int"],
            "ejer_ladefacilitet" => ["ejer", false, "varchar"],
            "gyldig_fra" => ["gyldig_fra", false, "date"],
            "gyldig_til" => ["gyldig_til", false, "date"],
            "internationalt_id" => ["int_id", false, "varchar"],
            "internationalt_id1" => ["int_id1", false, "varchar"],
            "internationalt_id2" => ["int_id2", false, "varchar"],
            "internationalt_id3" => ["int_id3", false, "varchar"],
            "internationalt_id4" => ["int_id4", false, "varchar"],
            "internationalt_id5" => ["int_id5", false, "varchar"],
            "kontak_ved" => ["kontak_ved", false, "varchar"],
            "ladefacilitet_type_kode" => ["lade_ty_k", true, "int"],
            "link" => ["link", false, "varchar"],
            "link1" => ["link", false, "varchar"],
            "link2" => ["link", false, "varchar"],
            "link3" => ["link", false, "varchar"],
            "link4" => ["link", false, "varchar"],
            "sagsnr" => ["sagsnr", false, "varchar"],
            "planstatus_kode" => ["planstat_k", false, "int"],

            "operatoer_ladefacilitet" => ["operatoer", false, "varchar"],
            "udbyder_ladefacilitet" => ["udbyder", false, "varchar"],
            "stiktype" => ["stiktype", false, "varchar"],
            "tilgaengelighed_type_kode" => ["tilgae_ty_k", false, "int"],
            "omraade_navn" => ["omr_navn", false, "varchar"],

            "geometri" => ["the_geom", true, "geometry", self::MULTIPOINT],
        ],
        5608 => [
            "objekt_id" => ["objekt_id", false, "uuid"],
            "cvr_kode" => ["cvr_kode", true, "int"],
            "bruger_id" => ["bruger_id", true, "varchar"],
            "oprindkode" => ["oprindkode", true, "int"],
            "statuskode" => ["statuskode", true, "int"],
            "off_kode" => ["off_kode", true, "int"],
            "noegle" => ["noegle", false, "varchar"],
            "note" => ["note", false, "varchar"],
            "link" => ["link", false, "varchar"],

            "planstatus_kode" => ["planstat_k", false, "int"],
            "beliggenhedskommune" => ["belig_kom", false, "int"],

            "id_cykelknudepkt" => ["id_cykelkp", true, "varchar"],
            "nodenumber" => ["nodenumber", false, "varchar"],
            "ismain" => ["ismain", false, "bool"],
            "deadend" => ["deadend", true, "bool"],
            "refmain" => ["refmain", false, "varchar"],
            "afm_cykelknudepkt" => ["afm_kpkt", true, "bool"],

            "geometri" => ["the_geom", true, "geometry", self::MULTIPOINT],
        ],
        5609 => [
            "objekt_id" => ["objekt_id", false, "uuid"],
            "cvr_kode" => ["cvr_kode", true, "int"],
            "bruger_id" => ["bruger_id", true, "varchar"],
            "oprindkode" => ["oprindkode", true, "int"],
            "statuskode" => ["statuskode", true, "int"],
            "off_kode" => ["off_kode", true, "int"],
            "noegle" => ["noegle", false, "varchar"],
            "note" => ["note", false, "varchar"],
            "link" => ["link", false, "varchar"],

            "planstatus_kode" => ["planstat_k", false, "int"],
            "beliggenhedskommune" => ["belig_kom", false, "int"],

            "id_cykelknudepunktsstraekning" => ["id_cykelks", true, "varchar"],
            "length" => ["length", false, "double precision"],
            "privatenot" => ["privatenot", true, "bool"],
            "surfacenot" => ["surfacenot", true, "bool"],
            "onewaynot" => ["onewaynot", true, "bool"],

            "geometri" => ["the_geom", true, "geometry", self::MULTILINESTRING],
        ],
        5610 => [
            "objekt_id" => ["objekt_id", false, "uuid"],
            "cvr_kode" => ["cvr_kode", true, "int"],
            "bruger_id" => ["bruger_id", true, "varchar"],
            "oprindkode" => ["oprindkode", true, "int"],
            "statuskode" => ["statuskode", true, "int"],
            "off_kode" => ["off_kode", true, "int"],
            "noegle" => ["noegle", false, "varchar"],
            "note" => ["note", false, "varchar"],
            "link" => ["link", false, "varchar"],

            "planstatus_kode" => ["planstat_k", false, "int"],
            "beliggenhedskommune" => ["belig_kom", false, "int"],



            "geometri" => ["the_geom", true, "geometry", self::MULTILINESTRING],
        ],
    ];
}
