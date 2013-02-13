<?php
// PHP-Programm nach dem Programmablaufplan für die maschinelle Berechnung
// der vom Arbeitslohn einzubehaltenden Lohnsteuer, des Solidrätätszuschlags und
// der Maßstabsteuer für die Kirchenlohnsteuer 2013
// Version 11.2.2013, 8.130 Euro Grundfreibetrag berücksichtigt
// Der Programmablaufplan 2013 findet sich auf den Internetseiten des BMF zum
// Herunterladen unter   http://www.bundesfinanzministerium.de/Content/DE/Downloads/Steuern/Steuerarten/Lohnsteuer/Programmablaufplan/2012-11-19-PAP-2013.pdf?__blob=publicationFile&v=2


  error_reporting(E_ALL);

  function init_post() {
    $_POST['aganzeige']	    =  isset($_POST['aganzeige'])	     ? $_POST['aganzeige']	 :  "0" ;
    $_POST['ajahr']         =  isset($_POST['ajahr']     )       ?  $_POST['ajahr']     :  "0" ;
    $_POST['bundesland']    =  isset($_POST['bundesland'])       ?  $_POST['bundesland']:  "6" ;
    $_POST['e_bg']          =  isset($_POST['e_bg']      )       ?  $_POST['e_bg']      :  "0,8 " ;
    $_POST['e_krv']         =  isset($_POST['e_krv']     )       ?  $_POST['e_krv']     :  "0" ;
    $_POST['e_kvp']         =  isset($_POST['e_kvp']     )       ?  $_POST['e_kvp']     :  " " ;
    $_POST['e_pkpv']        =  isset($_POST['e_pkpv']    )       ?  $_POST['e_pkpv']    :  " " ;
    $_POST['e_pkv']         =  isset($_POST['e_pkv']     )       ?  $_POST['e_pkv']     :  "1" ;
    $_POST['e_u1']          =  isset($_POST['e_u1']      )       ?  $_POST['e_u1']      :  "1,1" ;
    $_POST['e_u2']          =  isset($_POST['e_u2']      )       ?  $_POST['e_u2']      :  "0,1" ;
    $_POST['entsch_1']	    =   isset($_POST['entsch_1'])	     ?  $_POST['entsch_1']	:  " " ;
    $_POST['entsch_2']	    =  isset($_POST['entsch_2'])	     ?  $_POST['entsch_2']	:   " " ;
    $_POST['f']             =  isset($_POST['f']         )       ?  $_POST['f']         :  " " ;
    $_POST['jfreib']        =  isset($_POST['jfreib']    )       ?  $_POST['jfreib']    :  " " ;
    $_POST['jhinzu']        =  isset($_POST['jhinzu']    )       ?  $_POST['jhinzu']    :  " " ;
    $_POST['jre4ent']       =  isset($_POST['jre4ent']   )       ?  $_POST['jre4ent']   :  " " ;
    $_POST['jsonstb']       =  isset($_POST['jsonstb']   )       ?  $_POST['jsonstb']   :  " " ;
    $_POST['kapindex']	    =  isset($_POST['kapindex'])	     ?  $_POST['kapindex']	:   "0" ;
    $_POST['kinderlos']     =	    isset($_POST['kinderlos'])	 ?  $_POST['kinderlos'] :  "1" ;
    $_POST['kvsatz']        =  isset($_POST['kvsatz']    )       ?  $_POST['kvsatz']    :  "15.5" ;
    $_POST['lzz']           =  isset($_POST['lzz']       )       ?  $_POST['lzz']       :  "2" ;
    $_POST['r']             =  isset($_POST['r']         )       ?  $_POST['r']         :  "0" ;
    $_POST['re4']           =  isset($_POST['re4']       )       ?  $_POST['re4']       :  "2500" ;
    $_POST['sonstb']        =  isset($_POST['sonstb']    )       ?  $_POST['sonstb']    : " " ;
    $_POST['sonstent']      =	  isset($_POST['sonstent'])	     ?  $_POST['sonstent']  : " " ;
    $_POST['sterbe']        =  isset($_POST['sterbe']    )       ?  $_POST['sterbe']    : " " ;
    $_POST['stkl']          =  isset($_POST['stkl']      )       ?  $_POST['stkl']      : "1" ;
    $_POST['vbez']          =  isset($_POST['vbez']      )       ?  $_POST['vbez']      : " " ;
    $_POST['vbezm']         =  isset($_POST['vbezm']     )       ?  $_POST['vbezm']     : " " ;
    $_POST['vbezs']         =  isset($_POST['vbezs']     )       ?  $_POST['vbezs']     : " " ;
    $_POST['vjahr']         =  isset($_POST['vjahr']     )       ?  $_POST['vjahr']     : "2013" ;
    $_POST['vkapa']         =  isset($_POST['vkapa']     )       ?  $_POST['vkapa']     : " " ;
    $_POST['vmt']           =  isset($_POST['vmt']       )       ?  $_POST['vmt']       : " " ;
    $_POST['zkf']           =  isset($_POST['zkf']       )       ?  $_POST['zkf']       : "0" ;
    $_POST['zmvb']          =  isset($_POST['zmvb']      )       ?  $_POST['zmvb']      : "12" ;
  }

  function lst2013() {  //Programmablaufplan 2013, Steuerung S.10

    global $jre4, $zre4j, $jsonstb, $jvbez, $vbezso, $zvbezj, $kennz, $kennvmt, $lstjahr, $st, $jw, $lstlzz, $anteil1, $zkf, $ztabfb, $kfb, $jbmg, $jvbez, $f;

    init_post();

    mre4jl();
    $jre4 = ($zre4j*100 + $jsonstb);  // Voraussichtlicher Jahreslohn für Sonstige Bezüge
    $jvbez = $zvbezj*100;            // darin enthaltene Versorgungsbezüge
                                    // nicht im PAP!
    $vbezso = 0;
    $kennvmt=0;
    mre4();
    mre4abz();
    mztabfb();
    mlstjahr();
    $lstjahr = floor($st*$f);
    uplstlzz();
    upvkvlzz();

    if ($zkf > 0) {
       $ztabfb = $ztabfb + $kfb;
       mlstjahr();
       $jbmg = floor($st * $f);
     }
     else
       $jbmg = $lstjahr;
    msolz();
    msonst();
    mvmt();
  }

  function mre4jl() {
 //Ermittlung des Jahresarbeitslohns und der Freibeträge, PAP 2013, S. 11

 global $jlfreib, $jlhinzu, $jfreib, $jhinzu, $lzz, $zre4j, $re4, $zvbezj, $vbez, $lzzfreib, $lzzhinzu, $af;

    if ($lzz == 1) {
       $zre4j = $re4 / 100;
       $zvbezj = $vbez / 100;
       $jlfreib = $lzzfreib /100;
       $jlhinzu = $lzzhinzu / 100;
    }
    if ($lzz == 2) {
       $zre4j = ($re4 * 12) /100;
       $zvbezj = ($vbez * 12) /100;
       $jlfreib = ($lzzfreib * 12) /100;
       $jlhinzu = ($lzzhinzu * 12) / 100;
    }
    if ($lzz == 3) {
       $zre4j = ($re4 * 360 / 7) /100;
       $zvbezj = ($vbez * 360 / 7) /100;
       $jlfreib = ($lzzfreib * 360 / 7) /100;
       $jlhinzu = ($lzzhinzu * 360 / 7) / 100;
    }
    if ($lzz == 4) {
       $zre4j = ($re4 * 360) /100;
       $zvbezj = ($vbez * 360) /100;
       $jlfreib = ($lzzfreib * 360) /100;
       $jlhinzu = ($lzzhinzu * 360) / 100;
    }

       $jlfreib = $jfreib / 100;   //da Jahres-Beträge übernommen werden
       $jlhinzu = $jhinzu / 100;


    if ($zre4j < 0)                 // nicht im PAP
        $zre4j = 0;

    if ($zvbezj <=0 || $vbez<=0)
        $zvbezj = 0;

    if($af = 0) $f=1;
}

  function mre4() {
   //Freibetraege fuer Versorgungsbezuege, Altersentlastungsbetrag (§39b Abs. 2 Satz 2 EStG) PAP Seite 12

  global $zvbez, $fvbz, $fvb, $vbezbso, $fvbzso, $fvbso, $vjahr, $j, $lzz, $vbezb, $vbezm, $zmvb, $vbezs, $hfvb, $vbezso, $vkapa, $hfvbzso, $alter1, $alte, $ajahr, $k, $bmg, $zre4j, $zvbezj, $hbalte;

  $tab1 = Array (0, 0.4, 0.384, 0.368, 0.352, 0.336, 0.32, 0.304, 0.288, 0.272, 0.256, 0.24, 0.224, 0.208, 0.192, 0.176, 0.16, 0.152, 0.144, 0.136, 0.128, 0.12, 0.112, 0.104, 0.096, 0.088, 0.08, 0.072, 0.064, 0.056, 0.048, 0.04, 0.032, 0.024, 0.016, 0.008, 0);
    $tab2 = Array (0, 3000, 2880, 2760, 2640, 2520, 2400, 2280, 2160, 2040, 1920, 1800, 1680, 1560, 1440, 1320, 1200, 1140, 1080, 1020, 960, 900, 840, 780, 720, 660, 600, 540, 480, 420, 360, 300, 240, 180, 120, 60, 0);
    $tab3= Array (0, 900, 864, 828, 792, 756, 720, 684, 648, 612, 576, 540, 504, 468, 432, 396, 360, 342, 324, 306, 288, 270, 252, 234, 216, 198, 180, 162, 144, 126, 108, 90, 72, 54, 36, 18, 0);
    $tab4 = $tab1;
    $tab5 = Array (0,1900, 1824, 1748, 1672, 1596, 1520, 1444, 1368, 1292, 1216, 1140, 1064, 988, 912, 836, 760, 722, 684, 646, 608, 570, 532, 494, 456, 418, 380, 342, 304, 266, 228, 190, 152, 114, 76, 38, 0);

   if ($zvbezj == 0) {
       $fvbz = 0;
       $fvb = 0;
       $fvbzso = 0;
       $fvbso = 0;
       }
     else {
       if ($vjahr < 2006)
          $j = 1;
        else {
          if ($vjahr < 2040)
             $j = $vjahr - 2004;
          else
             $j = 36;
        }

       if ($lzz == 1) {

             $vbezb = $vbezm * $zmvb + $vbezs;
             $hfvb = $tab2[$j] / 12 * $zmvb;
             $fvbz = ceil($tab3[$j] / 12 * $zmvb);

          }
            else {

             $vbezb = $vbezm * $zmvb + $vbezs;
             $hfvb = $tab2[$j];
             $fvbz = $tab3[$j];
           }

       $fvb = ceil(floor($vbezb * $tab1[$j]*100))/10000;

       if ($fvb > $hfvb)
          $fvb = $hfvb;

       $fvbso = $fvb + ceil(floor($vbezbso * $tab1[$j]*100))/10000;

        if ($fvbso > $tab2[$j])
        $fvbso = $tab2[$j];

       $hfvbzso = ($vbezb + $vbezbso) /100 - $fvbso;
       $fvbzso = ceil($fvbz + $vbezbso/100);

       if ($fvbzso > $hfvbzso)
           $fvbzso = ceil($hfvbzso);

       if ($fvbzso > $tab3[$j])
           $fvbzso = $tab3[$j];

         $hfvbz = ($vbezb / 100) - $fvb;

         if($fvbz > $hfvbz)
           $fvbz = ceil($hfvbz);
    }

    if ($alter1 == 0)
       $alte = 0;

    else {
       if ($ajahr < 2006)
          $k = 1;
       else {
          if ($ajahr < 2040)
             $k = $ajahr - 2004;
          else
             $k = 36;
      }

       $bmg = $zre4j - $zvbezj;
       $alte = ceil($bmg * $tab4[$k]);
       $hbalte = $tab5[$k];

       if ($alte > $hbalte)
          $alte = $hbalte;
   }
 }

  function mre4abz() {
   // Ermittlung des Jahresarbeitslohns nach Abzug der Freibeträge, PAP 2013, S. 15

   global $zre4, $zre4j, $fvb, $alte, $jlfreib, $jlhinzu, $zre4vp, $zvbez, $zvbezj, $entsch, $kennvmt;

   $zre4 = $zre4j - $fvb - $alte - $jlfreib + $jlhinzu;

   if($zre4 < 0)
     $zre4 = 0;
   $zre4vp = $zre4j;
   if($kennvmt == 2)
    $zre4vp = $zre4vp - $entsch/100;
   $zvbez = $zvbezj - $fvb;
   if($zvbez < 0)
    $zvbez = 0;
   }

  function mztabfb() {
  //Ermittlung der festen Tabellenfreibetraege (ohne Vorsorgepauschale) PAP Seite 16

  global $kfb, $vbez, $zvbez, $fvbz, $fvbzso, $zre4, $anp, $stkl, $zkf, $ztabfb, $kztab;


    $efa = 0;
    $sap = 0;
    $kfb = 0;

    $anp = 0;
    if ($zvbez > 0) {
       if ($zvbez < $fvbz)
          $fvbz = floor($zvbez);            // auf Euro abrunden (nach 2.1 PAP)
    }
    if ($stkl < 6) {
       if ($zvbez > 0) {
          if ($zvbez - $fvbz < 102)
             $anp = ceil($zvbez - $fvbz);  // auf Euro aufrunden
            else
             $anp = 102;
       }
    }
    else {
     $fvbz = 0;
     $fvbzso = 0;
     }

    if ($stkl < 6) {
       if ($zre4 > $zvbez) {
          if ($zre4 - $zvbez < 1000)
             $anp = ceil($anp + $zre4 - $zvbez);
            else
             $anp = $anp + 1000;
       }
    }

       $kztab = 1;

   if ($stkl == 1) {
       $sap = 36;
       $kfb = $zkf * 7008;
    }
    if ($stkl == 2) {
       $efa = 1308;
       $sap = 36;
       $kfb = $zkf * 7008;
    }
    if ($stkl == 3) {
       $kztab = 2;
       $sap = 36;
       $kfb = $zkf * 7008;
   }
    if ($stkl == 4) {
       $sap = 36;
       $kfb = $zkf * 3504;
    }
    if ($stkl == 5){
       $sap = 36;
       $kfb = 0;
    }
    if ($stkl == 6)
       $kfb = 0;

    $ztabfb = $efa + $anp + $sap + $fvbz;
 }


  function mlstjahr() {  //Ermittlung der Jahreslohnsteuer, PAP S. 17

  global $st, $zre4, $kennvmt, $zve, $stkl, $zre4vmt, $ztabfb, $vmt, $vkapa, $vsp, $kztab, $x, $stovmt;

       upevp();

    if($kennvmt != 1){
      $zve = $zre4 - $ztabfb - $vsp;
      upmlst();
      }

     else {
       $zve = $zre4 - $ztabfb - $vsp - $vmt/100 - $vkapa/100;

    if($zve < 0) {
       $zve = ($zve + $vmt/100 + $vkapa/100)/5;
       upmlst();
       $st = $st * 5;
    }

    else {
    upmlst();
    $stovmt = $st;
    $zve = $zve + ($vmt+$vkapa)/500;
    upmlst();
    $st = ($st - $stovmt)*5 + $stovmt;
    }
   }
  }

  function upvkvlzz() {  //neu PAP 2013 S. 19

  global $jw, $vkv, $anteil1, $vkvlzz;

  upvkv();
  $jw = $vkv;
  upanteil();
  $vkvlzz = $anteil1;

  }

  function upvkv() { //neu PAP 2013 S. 19

  global $pkv,$vsp2,$vsp3,$vkv;

  if($pkv > 0){
   if($vsp2 > $vsp3)
    $vkv = $vsp2 * 100;
   else
    $vkv = $vsp3 * 100;
  }
  else
   $vkv = 0;

  }

  function uplstlzz() {  // PAP 2013 S. 20

  global $jw, $lstjahr, $lstlzz, $anteil1;

   $jw = $lstjahr * 100;
   upanteil();
   $lstlzz = $anteil1;

  }


  function upmlst() {            // unter eigener Funktion im PAP, S. 18

  global $zve, $stkl, $kztab, $x;


    if ($zve < 1) {
       $zve = 0;
       $x = 0;
     }
     else
       $x = floor($zve / $kztab);  // auf Euro abrunden

     if ($stkl < 5)
       uptab13();
     else
       mst5_6();
  }

  function upevp() {
  // Vorsorgepauschale (§39b Abs. 2 Satz 6 Nr 3 EStG)PAP Seite 19

  global $krv, $zre4vp, $kztab, $vsp, $vsp1, $vsp2, $stkl, $vspn, $jvbez;


    if ($krv > 1)
         $vsp1 = 0;
    else {
    if ($krv == 0) {
     if($zre4vp > 69600)
      $zre4vp = 69600;
    }
    else {
      if ($zre4vp > 58800)
        $zre4vp = 58800;
    }
    $vsp1 = 0.52 * $zre4vp;
    $vsp1 = $vsp1 * 0.0945;
    }

    $vsp2 = 0.12 * $zre4vp;
    if($stkl == 3)
      $vhb = 3000;
    else
      $vhb = 1900;

    if ($vsp2 > $vhb)
       $vsp2 = $vhb;
    $vspn = ceil($vsp1 + $vsp2);  //auf Euro aufrunden
    mvsp();
    if ($vspn > $vsp)
       $vsp = $vspn;
}

 function mvsp() {
 //Vorsorgepauschale (§39b Abs. 2 Satz 5 Nr 3 EStG) Vergleichsberechnung
 //fuer Guenstigerpruefung PAP Seite 20

  global $zre4vp, $krv, $pkv, $pkpv, $pvz, $pvs, $vsp3, $stkl, $vsp1, $vsp;

   if($zre4vp > 47200)
     $zre4vp = 47200;

  if($pkv > 0){
     if($stkl==6)
      $vsp3 = 0;
    else {
     $vsp3 = $pkpv * 12;

     if($pkv == 2) {
       $kvsatzag = 0.07;
     if($pvs == 1)
       $pvsatzag = 0.00525;
     else
       $pvsatzag = 0.01025;
     $vsp3 = $vsp3 - $zre4vp * ($kvsatzag + $pvsatzag);
    }
   }
  }
    else {
     $kvsatzan = 0.079;
    if($pvs == 1)
     $pvsatzan = 0.01525;
    else
     $pvsatzan = 0.01025;
    if($pvz == 1)
     $pvsatzan = $pvsatzan + 0.0025;
    $vsp3 = $zre4vp * ($kvsatzan + $pvsatzan);
     }
    $vsp = ceil($vsp3 + $vsp1);
  }


    function mst5_6() {
    // Lohnsteuer fuer die Steuerklassen V und VI (§ 39b Abs. 2 Satz 7 EStG) PAP Seite 21

    global $zx, $x, $st;

    $zzx = $x;


  if ($zzx > 26441) {
       $zx = 26441;
       up5_6();
       if ($zzx > 200584){
        $st = $st + (200584 - 26441) * 0.42;
        $st = floor($st + ($zzx - 200584) * 0.45);
       }
       else
        $st = floor($st + ($zzx-26441) * 0.42);
     }
     else {
       $zx = $zzx;
       up5_6();
       if ($zzx > 9429) {
          $vergl = $st;
          $zx = 9429;
          up5_6();
          $hoch = floor($st + ($zzx - 9429) * 0.42);
          if ($hoch < $vergl)
             $st = $hoch;
           else
             $st = $vergl;
        }
     }
  }


  function up5_6() {

  global $x, $zx, $st;

    $x = $zx * 1.25;
    uptab13();
    $st1 = $st;
    $x = $zx * 0.75;
    uptab13();
    $st2 = $st;
    $diff = ($st1 - $st2) * 2;
    $mist = floor($zx * 0.14);
    if ($mist > $diff)
       $st = $mist;
     else
       $st = $diff;
 }

 function msolz() {   // Solidaritätszuschlag, PAP S. 22

 global $kztab, $jbmg, $jw, $anteil1, $solzlzz, $bk, $r;

    $solzfrei = 972 * $kztab;
    if ($jbmg > $solzfrei) {
       $solzj = $jbmg * 5.5 / 100;
       $solzmin = ($jbmg - $solzfrei) * 20 / 100;
       if ($solzmin < $solzj)
          $solzj = $solzmin;
       $jw = $solzj * 100;
       upanteil();
       $solzlzz = $anteil1;
     }
     else
       $solzlzz = 0;

     if ($r > 0){
       $jw = $jbmg * 100;
       upanteil();
       $bk = $anteil1;
       }
     else
       $bk = 0;
    }


 function upanteil() {  // Anteil der Jahresbeiträge für einen LZZ
                        // PAP 2013, S. 23
                        // anteil1 abrunden, anteil2 wird nicht gebraucht

    global $lzz, $jw, $anteil1, $anteil2;

    if ($lzz == 1) {
       $anteil1 = $jw;
       $anteil2 = $jw;
    }
    if ($lzz == 2) {
       $anteil1 = floor(round($jw * 100 / 12)/100);
       $anteil2 = ceil($jw / 12);
    }
    if ($lzz == 3) {
       $anteil1 = floor(round($jw * 700 / 360)/100);
       $anteil2 = ceil($jw * 7 / 360);
    }
    if ($lzz == 4) {
       $anteil1 = floor(round($jw * 100 / 360)/100);
       $anteil2 = ceil($jw / 360);
    }
  }

 function msonst() {  // Berechnung sonstiger Bezüge, PAP 2013, S. 24

     global  $lzz, $lstoso, $zmvb, $sonstb, $zre4j, $jre4, $sonstb, $zvbezj, $jvbez, $vbs, $lstso, $st, $sts, $lstoso, $solzs, $r, $bks, $sterbe, $vbezbso, $f, $vkvsonst, $vkv;

       $lzz = 1;
       if($zmvb == 0)
         $zmvb = 12;
       if ($sonstb > 0) {
       mosonst();
       upvkv();
       $vkvsonst = $vkv;
       $zre4j = ($jre4 + $sonstb) / 100;
       $zvbezj = ($jvbez + $vbs) / 100;
       $vbezbso = $sterbe;
       mre4sonst();
       mlstjahr();
       upvkv();
       $vkvsonst = $vkv - $vkvsonst;
       $lstso = $st * 100;
       $sts = floor(($lstso - $lstoso) * $f);

       if($sts < 0)
         $sts = 0;
       $solzs = floor($sts * 5.5 / 100);
       if ($r > 0)
          $bks = $sts;
        else
          $bks = 0;
     }
    else {
       $vkvsonst = 0;
       $lstso = 0;
       $sts = 0;
       $solzs = 0;
       $bks = 0;
    }
}



function mvmt() {  // Berechnung der Vergütung für mehrjährige Tätigkeit
                   // PAP 2013, S. 25

     global   $zre4vp, $jre4ent, $sonstent, $ztabfb, $fvbz, $fvbzoso, $vkapa, $vmt, $lstso, $lstoso, $vbezbso, $sterbe, $zre4j, $jre4, $sonstb, $zvbezj, $jvbez, $vbs, $st, $kennvmt, $stv, $solzv, $r, $bkv, $lst2, $lst3, $lst1, $f;

       if($vkapa < 0)
        $vkapa = 0;
    if ($vmt + $vkapa > 0) {
       if($lstso == 0) {
         mosonst();
         $lst1 = $lstoso;
       }
       else
         $lst1 = $lstso;

     $vbezbso = $sterbe + $vkapa;
     $zre4j = ($jre4 + $sonstb + $vmt + $vkapa) / 100;
     $zvbezj = ($jvbez + $vbs + $vkapa) / 100;
     $kennvmt = 2;
     mre4sonst();
     mlstjahr();
     $lst3 = $st * 100;
     mre4abz();
     $zre4vp = $zre4vp - $jre4ent/100 - $sonstent/100;
     $kennvmt=1;
     mlstjahr();
     $lst2 = $st * 100;
     $stv = $lst2 - $lst1;
     $lst3 = $lst3 - $lst1;

        if ($lst3 < $stv)
          $stv = $lst3;
       if ($stv < 0)
          $stv = 0;
       else
          $stv = floor($stv * $f);
       $solzv = floor($stv * 5.5) / 100;
       if ($r > 0)
          $bkv = $stv;
        else
          $bkv = 0;
     }
     else {
       $stv = 0;
       $solzv = 0;
       $bkv = 0;
    }
 }

   function mosonst() {
    // Sonderberechnung ohne sonstige Bezüge für Berechnung sonstige Bezüge/Vergütung
    // mehrjährige Tätigkeit. PAP 2013, S. 26

    global $zre4j, $zre4vp, $jre4, $jre4ent, $zvbezj, $jvbez, $jlfreib, $jlhinzu, $jfreib, $jhinzu, $lstoso, $st;

    $zre4j = $jre4 / 100;
    $zvbezj = $jvbez / 100;
    $jlfreib = $jfreib / 100;
    $jlhinzu = $jhinzu / 100;
    mre4();
    mre4abz();
    $zre4vp = $zre4vp - $jre4ent/100;
    mztabfb();
    mlstjahr();
    $lstoso = $st * 100;
  }

  function mre4sonst() {
   // Sonderberechnung mit sonstigen Bezüge für Berechnung sonstige Bezüge/Vergütung
    // mehrjährige Tätigkeit. PAP 2013, S. 26

    global $fvb, $fvbso, $fvbz, $fvbzso, $zre4vp, $jre4ent, $sonstent;

    mre4();
    $fvb = $fvbso;
    mre4abz();
    $zre4vp = $zre4vp - $jre4ent/100 - $sonstent/100;
    $fvbz = $fvbzso;
    mztabfb();

 }

 function uptab13() {  //Tarifliche Einkommensteuer, PAP 2013, S. 27

  global $x, $st, $kztab, $abjuli;

         if ($x < 8131)
          $st = 0;
        else {
        if ($x < 13470) {
           $y = ($x - 8130) / 10000;
           $rw = $y * 933.70;
           $rw = $rw + 1400;
           $st = floor($rw * $y);
         }
         else {
           if ($x < 52882) {
              $y = ($x - 13469) / 10000;
              $rw = $y * 228.74;
              $rw = $rw + 2397;
              $rw = $rw * $y;
              $st = floor($rw + 1014);
            }
         else {
            if( $x < 250731)
                $st = floor($x * 0.42 - 8196);
         else
                $st = floor($x * 0.45 - 15718);
               }
           }
       }

    $st = $st * $kztab;
}

  // --- Ende PAP 2013 -----------------------------------------------

  function jahranteil() { // Berechnung Jahreslohn aus LZZ-Lohn
                          // (nicht im PAP enthalten)
                          // für Berechnung sonstige bzw. mehrjährige Bezüge notwendig.

  global $lzz, $jw, $anteil1, $anteil2;

    if ($lzz == 1){
       $anteil1 = $jw;
       $anteil2 = $anteil1;
       }
    if ($lzz == 2){
       $anteil1 = $jw * 12;
       $anteil2 = $jw/12;
     }
    if ($lzz == 3){
       $anteil1 = $jw * 360 / 7;
       $anteil2 = $jw * 7/360;
       }
    if ($lzz == 4){
       $anteil1 = $jw * 360;
       $anteil2 = $jw/360;
    }
  }

  function komma($wert) {

  $laenge = strlen($wert);
  $pos = strrpos($wert,".");
  if($pos == false)
    $wert = $wert.".00";
  elseif ($laenge - $pos == 2)
    $wert = $wert."0";

  $wert1 = number_format($wert*1000, 2, '', '.');
  $laenge = strlen($wert1);
  $wert = substr($wert1,-$laenge,-1);
  return $wert;
  }

  // ------ Berechnung Sozialabgaben ---------------

   function soz() {

       global $zmvb, $vbez, $kapindex, $sterbe, $kvwertvb, $jvbez, $bemesk, $bemesr, $jre4, $re4soz, $lzzsoz, $land, $sonstb, $jsonstb, $krv, $lzz, $pvz, $kvsatz, $anteil1, $anteil2, $jw, $rentewert, $rentewertag, $kvwert, $kvwertag, $pflegewert, $pflegewertag, $aloswert, $aloswertag, $pvs, $pkv, $kvp, $sozproz, $sozprozvb, $jvbez, $jre4ret, $re4ret, $sterbesoz;

       $lzz = $lzzsoz;

       $pflege = 1.025;
       $pflege_ag = $pflege;
     if($land==13) {
         $pflege = 1.525;
         $pflege_ag = 0.525;
       }

       $pvzusatz = 0;
     if ($pvz == 1)
       $pvzusatz = 0.25;

      if($kvsatz < 14.9)
         $kvsatz=0;
      else
         $kvsatz = floor($kvsatz*10)/10 - 0.9;

        bemesberech();

      if($bemesk >= $re4soz){
       $bemesk = $re4ret/100 + ($sonstb-$sterbesoz)/100;
       }
     else {
         $restsonstb = max($bemesk-($jre4ret + $jsonstb),0);
         $jw = min($jre4ret,$bemesk);
         jahranteil();
         $bemesk = ($restsonstb + $anteil2)/100;
      }

     if($bemesr>=$re4soz)
       $bemesr = $re4ret/100 + ($sonstb-$sterbesoz)/100;
     else {
         $restsonstb = max($bemesr-($jre4ret + $jsonstb),0);
         $jw = min($jre4ret,$bemesr);
         jahranteil();
         $bemesr = ($restsonstb + $anteil2)/100;
       }

     $rente = 9.45;
     $alos = 1.5;
     $kzahn = 0.9;
     if($krv == 2) $bemesr=0;
     if($kvsatz == 0) $bemesk=0;

     $rentewert=round($bemesr*$rente)/100;
     $rentewertag=$rentewert;
     if($bemesk>0){
     $kvwert=round($bemesk*$kvsatz/2 + $bemesk*$kzahn)/100;
     $kvwertag=round($bemesk*$kvsatz/2)/100;
     $pflegewert=round($bemesk*$pflege + $bemesk*$pvzusatz)/100;
     $pflegewertag=round($bemesk*$pflege_ag)/100;
     }
     $aloswert=round($bemesr*$alos)/100;
     $aloswertag=$aloswert;


     $kvwertvb=0;
     $sterbesozrest = 0;
     $sozproz = $kvsatz + $kzahn + $pflege + $pvzusatz;
     $sozprozvb = 17.45 + $pvzusatz;
     if($kvsatz>0 && $jvbez>0){
      $bemeskvbganz = min(max(4725000-$re4soz,0),$jvbez + $sterbe*$kapindex);
      if($bemeskvbganz > $jvbez){
       $sterbesozrest = ($bemeskvbganz - $jvbez);
       $bemeskvb = $jvbez;
      }
      else {
       $bemeskvb = $bemeskvbganz;
      }
     $jw = $bemeskvb;
     jahranteil();
     $bemeskvb = $anteil2;
     $kvwertvb=round(($anteil2 + $sterbesozrest)* $sozprozvb/100)/100;
     }

     if ($kvp>0){
       if($pkv == 2){
       $jw = $kvp*100/2;
       jahranteil();
       $jw = min($anteil1,393360);
       jahranteil();
       $kvwertag = round($anteil2)/100;
       if($land==13){
       $jw = $kvp*100/2;
       jahranteil();
       $jw = min($anteil1,369732);
       jahranteil();
       $kvwertag = round($anteil2)/100;
       }
      }
      else
       $kvwertag = 0;

       $kvwert=$kvp-$kvwertag;
     }


   }

   function bemesberech() {

   global $bemesk, $re4soz, $kvp, $land, $bemesr;

    $bemesk = min($re4soz,4725000);
     if($kvp > 0)
        $bemesk = 0;

      if ($land==30||$land==4||$land==8||$land==13||$land==14||$land==16)
           $bemesr = min(5880000,$re4soz);

      else
           $bemesr = min(6960000,$re4soz);
   }


  //------------------------------- Parameterübergabe -----------------------
 init_post();
 $ajahr = $_POST['ajahr'];
 if($ajahr>2004)
  $alter1=1;
 $pvz = 0;
 $pvz = $_POST['kinderlos'];
 $stkl = $_POST['stkl'];
 $f = str_replace(",",".",($_POST['f']));
 $af = 0;
 if($f > 0 && $f < 1 && $stkl == 4)
   $af = 1;
 else $f=1;
 $zkf = $_POST['zkf'];
 $land = $_POST['bundesland'];
 $pvs = 0;
 if($land==13)
  $pvs = 1;
 $kist = $_POST['r'];
 if($kist==0) $r=0;
  else $r=1;
 $krv = $_POST['e_krv'];
 if ($land==3||$land==4||$land==8||$land==13||$land==14||$land==16 && $krv == 0)
  $krv = 1;

 $lzz = $_POST['lzz'];
 $re4 = str_replace(",",".",($_POST['re4']))*100;
 $sonstb = str_replace(",",".",($_POST['sonstb']))*100;
 $entsch1 = str_replace(",",".",($_POST['entsch_1']))*100;
 $entsch2 = str_replace(",",".",($_POST['entsch_2']))*100;
 $entsch = ($entsch1 + $entsch2);
 $jre4ent = str_replace(",",".",($_POST['jre4ent']))*100;
 $sonstent = str_replace(",",".",($_POST['sonstent']))*100;
 $jsonstb = ($_POST['jsonstb'])*100;
 $vmt = ($_POST['vmt'])*100;
 $jfreib = ($_POST['jfreib'])*100;  // ggflls in $lzzjfreib ändern (f. anderen Zeitraum)
 $jhinzu = ($_POST['jhinzu'])*100;  // ggflls in $lzzjhinzu ändern
 $vbez = str_replace(",",".",($_POST['vbez']))*100;
 $zmvb = $_POST['zmvb'];
 if($lzz==2) $zmvb=12;
 $vbezs = str_replace(",",".",($_POST['vbezs']))*100;
 $sterbe = str_replace(",",".",($_POST['sterbe']))*100;
 $vbs = $sterbe;
 $vkapa = ($_POST['vkapa'])*100;
 $vjahr = $_POST['vjahr'];
 $vbezm = str_replace(",",".",($_POST['vbezm']))*100;
 mre4jl();
 $re4soz = $zre4j*100 + $sonstb + $jsonstb - ($zvbezj*100 + $sterbe);
 $lzzsoz = $lzz;
 $re4ret = $re4 - $vbez;
 $jre4ret = ($zre4j - $zvbezj)*100;
 $kapindex = $_POST['kapindex'];
 $sterbesoz = $sterbe;
 $pkpv = str_replace(",",".",($_POST['e_pkpv']));
 $kvp = str_replace(",",".",($_POST['e_kvp']));
 $pkv = 0;
 $kvsatz = 0;
 if($pkpv > 0)
  $pkv = $_POST['e_pkv'];
 if($kvp == 0)
  $kvsatz = str_replace(",",".",($_POST['kvsatz']));
 if($kvsatz==0 && $pkpv==0)
  $pkv=1;

 $brutto = ($re4+$sonstb+$vmt+$vkapa)/100;

 // print_r($_POST);  // zur Überprüfung der übergebenen Parameter

 lst2013();  //Berechnung Lohnsteuer

 // ------------------ Ausgabe ------------------------------

  //echo "<html>";
  //echo "<head>";
  //echo "<title>Gehaltsrechner 2013, Ergebnis</title>";
  //echo "<style> TD {font-family: arial; font-size: 11px; text-align: right;}</style>";
  //echo "</head>";
  //echo "<body>";
  //echo "<p>&nbsp;<p>";
  //echo "<center><TABLE cellpadding=5 cellspacing=0 border=0><TR><TD><TABLE cellSpacing=0 cellPadding=3 border=0 width=360><TR bgColor=#909090 height=24><TD style='text-align: center' colspan=3 vAlign=middle><font color=#FFFFFF><b>Ergebnis Lohnsteuerberechnung 2013</b></font></TD></TR>";
  //echo "<TR><TD colspan=3>&nbsp;</TD></TR>";

 $steuer = floor($lstlzz+$stv+$sts)/100;
 $soli = floor($solzlzz+$solzs+$solzv)/100;
 $kirche = floor(($bk+$bkv+$bks)*$kist/100)/100;
 $stges = $steuer+$soli+$kirche;

 //echo "<TR><b><TD>Bruttolohn:</TD><TD> ".komma($brutto)."</TD><TD> &euro;</TD></TR><TR><TD colspan=3></TD></TR>";
 $info="";
 if($f > 0 && $f < 1)
 $info = "</b>(Ehegattenfaktor von ".$f." berücksichtigt)";
 //echo "<TR><TD><b>Lohnsteuer: ".$info. "</TD><TD> ".komma($steuer)."</TD><TD> &euro;</TD></TR>";
 //echo "<TR><TD>davon Steuer für laufenden Lohn </TD><TD>".komma($lstlzz/100)."</TD><TD> &euro;</TD></TR>";
 //echo "<TR><TD>davon Steuer für Einmalbezug </TD><TD> ".komma($sts/100)."</TD><TD> &euro;</TD></TR>";
 //echo "<TR><TD>davon Steuer für Vergütung mehrjäriger Tätigkeit </TD><TD>".komma($stv/100)."</TD><TD> &euro;</TD></TR>";
 //echo "<TR><TD><br> <b>Solidaritätszuschlag:</b> </TD><TD><br> ".komma($soli)."</TD><TD><br> &euro;</TD></TR>";
 //echo "<TR><TD><b>".$kist."% Kirchensteuer:</b></TD><TD> ".komma($kirche)."</TD><TD> &euro;</TD></TR>";

 //--------------- Auswertung und Ausgabe Sozialabgaben --------------------

      $kvsatz=0;
      if($kvp == 0)
        $kvsatz = str_replace(",",".",($_POST['kvsatz']));
      $aganzeige = $_POST['aganzeige'];
      $u1 = str_replace(",",".",($_POST['e_u1']))*0.01;
      $u2 = str_replace(",",".",($_POST['e_u2']))*0.01;
      $bg = str_replace(",",".",($_POST['e_bg']))*0.01;
       //print_r($_POST);

      soz();  // --- Sozialabgaben berechnen -----

      //echo "<TR><TD colspan=3>&nbsp;</TD></TR>";
      //if($pkv>0)
      //echo "<TR><TD>eigener Anteil private Kranken-/Pflegeversicherung: </TD><TD>".komma($kvwert)."</TD><TD>  &euro;</TD></TR>";
      //if($bemesk>0 && $pkv==0) {
      //echo "<TR><TD>Beitrag zur Krankenversicherung: </TD><TD>".komma($kvwert)."</TD><TD>  &euro;</TD></TR>";
      //echo "<TR><TD>Beitrag zur Pflegeversicherung: </TD><TD>".komma($pflegewert)."</TD><TD>  &euro;</TD></TR>";
      //}
      //if($kvwertvb>0) {
      //echo "<TR><TD align=right>".$sozprozvb."% (voller) Kranken- und Pflegeversicherungsbeitrag für Versorgungsbezüge: ";
      //if($sterbe>0 && $kapindex==1)
      //echo " (krankenversicherungspflichtige Kapitalauszahlung berücksichtigt)";
      //echo "</TD><TD vAlign=bottom align=right>".komma($kvwertvb)."</TD><TD vAlign=bottom>  &euro;</TD></TR>";
      //}
      //if($bemesr>0){
       //echo "<TR><TD>Beitrag zur Rentenversicherung: </TD><TD>".komma($rentewert)."</TD><TD>  &euro;</TD></TR>";
      //echo "<TR><TD>Beitrag zur Arbeitslosenversicherung: </TD><TD>".komma($aloswert)."</TD><TD>  &euro;</TD></TR>";
      //}
        //echo "<TR><TD colspan=3>&nbsp;</TD></TR>";
        //echo "<TR><TD><font color=#900000>Summe der Steuern:</font></TD><TD><font color=#900000> ".komma($stges)."</font></TD><TD><font color=#900000>  &euro;</font></TD></TR>";
       $sozges = $kvwert+$pflegewert+$rentewert+$aloswert+$kvwertvb;
       //echo"<TR><TD><font color=#009000>Summe Sozialversicherung:</font></TD><TD><font color=#009000> ".komma($sozges)."</font></TD><TD><font color=#009000>  &euro; </font></TD></TR>";
       $netto = $brutto-($stges+$sozges);
       //echo "<TR><TD><font color=#900090><b>Netto:</b></font> </TD><TD><font color=#900090><b>".komma($netto)."</b></font></TD><TD><font color=#900090>  <b>&euro;</b></font>";
       //echo "</font></TD></TR>";
      //if($aganzeige==1) {
      //echo "<TR><TD colspan=3><hr size=0></TD></TR>";
      //echo "<TR><TD style=\"text-align: right;\" colspan=3><font color=#0000FF><b>Arbeitgeberanteil</b></font> </TD></TR>";
      //if($pkv == 2)
      //echo "<TR><TD>Zuschuss zur privaten Kranken-/Pflegeversicherung: </TD><TD>".komma($kvwertag)."</TD><TD>  &euro;</TD></TR>";
      //if($bemesk>0 && $pkv==0) {
      //echo "<TR><TD>Krankenversicherung: </TD><TD>".komma($kvwertag)."</TD><TD>  &euro;</TD></TR>";
      //echo "<TR><TD>Pflegeversicherung: </TD><TD>".komma($pflegewertag)."</TD><TD>  &euro;</TD></TR>";
      //}
      //if($bemesr>0){
      //echo "<TR><TD>Rentenversicherung: </TD><TD>".komma($rentewertag)."</TD><TD>  &euro;</TD></TR>";
      //echo "<TR><TD>Arbeitslosenversicherung: </TD><TD>".komma($aloswertag)."</TD><TD>  &euro;</TD></TR>";
      //}
      $umlwert=0;
      if($u1>0) {
       if ($land==4||$land==8||$land==13||$land==14||$land==16)
           $jw = 5760000;
       else
           $jw = 6720000;
      jahranteil();
      $umlwert=min($re4ret,$anteil2);
      $u3 = min($re4ret + $sonstb, $anteil2)* 0.001;
      //echo "<TR><TD>Umlage U1:</TD><TD> ".komma(floor($umlwert*$u1)*0.01)."</TD><TD> &euro;</TD></TR>";
      //echo "<TR><TD>Umlage U2:</TD><TD> ".komma(floor($umlwert*$u2)*0.01)."</TD><TD> &euro;</TD></TR>";
      //echo "<TR><TD>Insolvenzumlage (0,15%):</TD><TD> ".komma((floor($u3*1.5)/100))."</TD><TD> &euro;</TD></TR>";
      //echo "<TR><TD>BG-Beitrag:</TD><TD> ".komma(floor($umlwert*$bg)*0.01)."</TD><TD> &euro;</TD></TR>";
      }
      if($kvp>0)
        $pflegewert=0;
      $agsumme=$kvwertag+$pflegewertag+$aloswertag+$rentewertag+$brutto+floor($umlwert*$u1)*0.01 + floor($umlwert*$u2)* 0.01 + floor($u3*1.5)* 0.01 + floor($umlwert*$bg)* 0.01;
      //echo "<TR><TD><font color=#000090><b>Arbeitgeber Gesamtbelastung</b></font>: </TD><TD><font color=#000090><b>".komma($agsumme)."</b></font></TD><TD><font color=#000090> <b>&euro;</b></font></TD></TR>";
      //}
      //echo "</TABLE>";
      //echo "</TD></TR></TABLE>";
      //echo "<p align=center><a href='javascript:goback()' style='text-decoration: none;font-family: arial;font-size: 8pt;'><b>&lt;&lt;&lt; zurück/neue Berechnung</b></font></a>";
      //echo "<p>&nbsp;</p>";
      //echo "<script> function goback() { history.go(-1); } </script>";
       //echo "</body>";
       //echo "</html>";

$outputs['bundesland']             = $_POST['bundesland'];
$outputs['geburtsdatum']           = $_POST['ajahr'];
$outputs['brutto']                 = $brutto;
$outputs['zeitraum']               = $_POST['lzz'];
$outputs['lohnsteuerklasse']       = $_POST['stkl'];
$outputs['kinderfrei_beitrag']     = $_POST['zkf'];
$outputs['sv_beitrag']             = $_POST['kvsatz'];
$outputs['kirchensteuer_beitrag']  = $_POST['r'];
$outputs['rv_pflichtig']           = $_POST['e_krv'];
$outputs['unter_23']               = $_POST['kinderlos'];
$outputs['lohnsteuer']             = $steuer;
$outputs['soliz']                  = $soli;
$outputs['kirchensteuer']          = $kirche;
$outputs['kv']                     = $kvwert;
$outputs['pv']                     = $pflegewert;
$outputs['rv']                     = $rentewert;
$outputs['av']                     = $aloswert;
$outputs['total_st']               = $stges;
$outputs['total_sv']               = $sozges;
$outputs['netto']                  = $netto;
?>
