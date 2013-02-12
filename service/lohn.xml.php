<?php
header ("Content-Type: application/xml");
require_once 'lohn13.php';
echo "<?xml version=\"1.0\" encoding=\"utf-8\" ?>\n";
?>
<instance>
    <input>
        <bundesland><?=$outputs['bundesland']?></bundesland>
        <geburtsdatum><?=$outputs['geburtsdatum']?></geburtsdatum>
        <brutto><?=$outputs['brutto']?></brutto>
        <zeitraum><?=$outputs['zeitraum']?></zeitraum>
        <lohnsteuerklasse><?=$outputs['lohnsteuerklasse']?></lohnsteuerklasse>
        <kinderfrei_beitrag><?=$outputs['kinderfrei_beitrag']?></kinderfrei_beitrag>
        <sv_beitrag><?=$outputs['sv_beitrag']?></sv_beitrag>
        <kirchensteuer_beitrag><?=$outputs['kirchensteuer_beitrag']?></kirchensteuer_beitrag>
        <rv_pflichtig><?=$outputs['rv_pflichtig']?></rv_pflichtig>
        <unter_23><?=$outputs['unter_23']?></unter_23>
    </input>
    <output>
        <lohnsteuer><?=$outputs['lohnsteuer']?></lohnsteuer>
        <soliz><?=$outputs['soliz']?></soliz>
        <kirchensteuer><?=$outputs['kirchensteuer']?></kirchensteuer>
        <kv><?=$outputs['kv']?></kv>
        <pv><?=$outputs['pv']?></pv>
        <rv><?=$outputs['rv']?></rv>
        <av><?=$outputs['av']?></av>
        <total_st><?=$outputs['total_st']?></total_st>
        <total_sv><?=$outputs['total_sv']?></total_sv>
        <netto><?=$outputs['netto']?></netto>
    </output>
</instance>