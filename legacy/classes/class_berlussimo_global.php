<?php

class berlussimo_global
{
    public $vermietete_einheiten = [];
    public $unvermietete_einheiten = [];

    function berlussimo_global()
    {
        $this->datum_heute = date("Y-m-d");
    }

    function unvermietete_einheiten_arr()
    {
        $db_abfrage = "SELECT MIETVERTRAG.MIETVERTRAG_ID, EINHEIT.EINHEIT_KURZNAME FROM MIETVERTRAG JOIN EINHEIT ON (EINHEIT.EINHEIT_ID = MIETVERTRAG.EINHEIT_ID) WHERE MIETVERTRAG_AKTUELL='1' && EINHEIT.EINHEIT_AKTUELL='1' &&  MIETVERTRAG.MIETVERTRAG_BIS<'$this->datum_heute' ORDER BY EINHEIT.EINHEIT_KURZNAME ASC,MIETVERTRAG.MIETVERTRAG_BIS  DESC";
        $result = DB::select($db_abfrage);
        if (!empty($result)) {
            foreach($result as $row) {
                $resultrow = ['einheit_kurzname' => $row['EINHEIT_KURZNAME'], 'mv_id' => $row['MIETVERTRAG_ID']];
                $this->unvermietete_einheiten [] = $resultrow;
            }
        }
    }

    function objekt_auswahl_liste()
    {
        session()->put('url.intended', URL::full());

        $mieten = new mietkonto ();
        if (session()->has('objekt_id')) {
            $objekt_kurzname = new objekt ();
            $objekt_kurzname->get_objekt_name(session()->get('objekt_id'));
            $mieten->erstelle_formular("Ausgewähltes Objekt: $objekt_kurzname->objekt_name", NULL);
        } else {
            $mieten->erstelle_formular("Objekt auswählen...", NULL);
        }
        echo "<div class='row'>";
        $objekte = new objekt ();
        $objekte_arr = $objekte->liste_aller_objekte();
        $anzahl_objekte = count($objekte_arr);
        for ($i = 0; $i < $anzahl_objekte; $i++) {
            $objekt_kurzname = ltrim(rtrim(htmlspecialchars($objekte_arr [$i] ["OBJEKT_KURZNAME"])));
            echo "<div class='col s6 m4 l2'>";
            echo "<a href='" . route('legacy::objekte::select', ['objekt_id' => $objekte_arr [$i] ['OBJEKT_ID']]) . "'>" . $objekt_kurzname . "</a>&nbsp;";
            echo "</div>";
        }
        echo "</div>";
        $mieten->ende_formular();
    }

    function monate_jahres_links($jahr, $link)
    {
        $f = new formular ();
        $f->fieldset("Monats- und Jahresauswahl", 'monate_jahre');
        $vorjahr = $jahr - 1;
        $nachjahr = $jahr + 1;
        $link_vorjahr = "&nbsp;<a href=\"$link&jahr=$vorjahr&monat=12\"><b>$vorjahr</b></a>&nbsp;";
        $link_nach = "&nbsp;<a href=\"$link&jahr=$nachjahr&monat=01\"><b>$nachjahr</b></a>&nbsp;";
        echo $link_vorjahr;
        $link_alle = "<a href=\"$link&jahr=$jahr\">Alle von $jahr</a>&nbsp;";
        echo $link_alle;
        for ($a = 1; $a <= 12; $a++) {
            $monat_zweistellig = sprintf('%02d', $a);
            $link_neu = "<a href=\"$link&monat=$monat_zweistellig&jahr=$jahr\">$a/$jahr</a>&nbsp;";
            // echo "$a/$jahr<br>";
            echo "$link_neu";
        }
        echo $link_nach;
        $f->fieldset_ende();
    }

    function jahres_links($jahr, $link)
    {
        $f = new formular ();
        $f->fieldset("Jahr wählen", 'monate_jahre');
        $vorjahr = $jahr - 1;
        $nachjahr = $jahr + 1;
        $link_vorjahr = "&nbsp;<a href=\"$link&jahr=$vorjahr\"><b>$vorjahr</b></a>&nbsp;";
        $link_nach = "&nbsp;<a href=\"$link&jahr=$nachjahr\"><b>$nachjahr</b></a>&nbsp;";
        echo $link_vorjahr;
        echo $link_nach;
        $f->fieldset_ende();
    }
} // ende class global