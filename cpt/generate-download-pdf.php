<?php
// download pdf
add_action('admin_post_download_pdf_action', 'download_pdf');
add_action('admin_post_nopriv_download_pdf_action', 'download_pdf');

function download_pdf() {
    $post_id = $_POST['selected_post_id'];
    $name = get_post_meta($post_id, 'zakaznik', true);
    $address = get_post_meta($post_id, 'adresa_instalace', true);
    $datetime = get_post_meta($post_id, 'datum', true);
    if (empty($datetime)) $datetime = get_the_date( 'Y-m-d H:i:s', $post_id);
    $balicek_id = get_post_meta($post_id, 'vyberte_balicek', true);
    $dotaci_id = get_post_meta($post_id, 'vyberte_dotaci', true);
    $dotace_title = get_post_meta($dotaci_id, 'nazev', true);
    $cena_konstrukce = get_post_meta($post_id, 'cena_konstrukce', true);
    
    list($date, $time) = explode(" ", $datetime);
    $date_pdf = str_replace('-', '.', $date);
    $date = str_replace('-', '', $date);
    $smlouva_number = $date.$post_id;

    $dotace_price = get_post_meta($post_id, 'dotace_vyse', true);
    if($dotace_price === ''){
        $dotace_price = 0;
    }
    $real_price = (int)get_post_meta($post_id, 'vlastni_investice_celkem', true);
    $cena_bez_dph = $real_price / 1.21;
    $cena_bez_dph = (int)$cena_bez_dph;
    $dph = (int)$cena_bez_dph * 0.21;
    $dph = (int)$dph;
    $cena_celkem = $cena_bez_dph + $dph;
    $final_price = $cena_celkem - (int)$dotace_price;


    $panel_id = get_post_meta($balicek_id, 'panel', true);
    $baterie_id = get_post_meta($balicek_id, 'baterie', true);
    $stridac_id = get_post_meta($balicek_id, 'stridac', true);
    
    $panel = get_field('panel', $balicek_id);
    $panel_name = $panel->post_title;
    $panel_vyrobce =  get_field('vyrobce', $panel_id);
    $panel_vyrobce_name = $panel_vyrobce->post_title;
    $panel_pocet = get_post_meta($post_id, 'pocet_panelu', true);
    $panel_popis = get_post_meta($panel_id, 'popis', true);

    $baterie = get_field('baterie', $balicek_id);
    $baterie_name = $baterie->post_title;
    $baterie_vyrobce =  get_field('vyrobce', $baterie_id);
    $baterie_vyrobce_name = $baterie_vyrobce->post_title;
    $baterie_pocet = get_post_meta($post_id, 'pocet_baterii', true);
    $baterie_popis = get_post_meta($baterie_id, 'popis', true);
    
    $stridac = get_field('stridac', $balicek_id);
    $stridac_name = $stridac->post_title;
    $stridac_vyrobce =  get_field('vyrobce', $stridac_id);
    $stridac_vyrobce_name = $stridac_vyrobce->post_title;
    $stridac_pocet = get_post_meta($post_id, 'pocet_stridacu', true);
    $stridac_popis = get_post_meta($stridac_id, 'popis', true);

    $balicek_komponenty = get_field('komponenty', $balicek_id);
    
    // $balicek = get_field('vyberte_balicek', $post_id);
    // $balicek_name = $balicek->post_title;

    // $name = iconv('utf-8', 'cp1250', $name);
    require_once WP_CONTENT_DIR . '/plugins/tecnickcom/tcpdf/tcpdf.php';
    require_once WP_CONTENT_DIR . '/plugins/setasign/fpdi/src/autoload.php';
    $pdf = new \setasign\Fpdi\Tcpdf\Fpdi('P','mm',array(250,350));
    $pagecount = $pdf->setSourceFile('templete1.pdf');

   
    $tplidx = $pdf->importPage(1);
    $pdf->AddPage();
    $pdf->useTemplate($tplidx);
    
    
    
    $pdf->SetTextColor(17, 115, 160);
    $pdf->SetFont('exo2b', '', 12);
    $pdf->writeHTMLCell(0, 2, 9, 92, "{$name}", 0, 0, 0, false, '', false);
    $date = date_create($name);


    $pdf->SetTextColor(17, 115, 160);
    $pdf->SetFont('exo2b', '', 14);
    $pdf->writeHTMLCell(0, 2, 140, 18, "{$smlouva_number}", 0, 0, 0, false, '', false);


    $pdf->SetTextColor(17, 115, 160);
    $pdf->writeHTMLCell(0, 2, 9, 115, "{$address}", 0, 0, 0, false, '', false);
    $date = date_create($address);

    $pdf->SetTextColor(17, 115, 160);
    $pdf->writeHTMLCell(0, 2, 9, 137, "{$date_pdf}", 0, 0, 0, false, '', false);
    $date = date_create($date_pdf);

    $tplidx = $pdf->importPage(2);
    $pdf->AddPage();
    $pdf->useTemplate($tplidx);

    $pdf->SetFont('exo2light', '', 12);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->writeHTMLCell(0, 2, 34.5, 70, "{$panel_vyrobce_name} - {$panel_name}", 0, 0, 0, false, '', false);
    $pdf->writeHTMLCell(0, 2, 220, 70, "{$panel_pocet}", 0, 0, 0, false, '', false);
    $pdf->writeHTMLCell(0, 2, 34.5, 82, "{$baterie_vyrobce_name} - {$baterie_name}", 0, 0, 0, false, '', false);
    $pdf->writeHTMLCell(0, 2, 220, 82, "{$baterie_pocet}", 0, 0, 0, true, '', false);
    $pdf->writeHTMLCell(0, 2, 34.5, 94, "{$stridac_vyrobce_name} - {$stridac_name}", 0, 0, 0, false, '', false);
    $pdf->writeHTMLCell(0, 2, 220, 94, "{$stridac_pocet}", 0, 0, 0, false, '', false);

    $i=0;
    foreach ($balicek_komponenty as $key => $value) {
        $y = $i* 12 + 106;
        $pdf->writeHTMLCell(0, 2, 34.5, $y, "{$value->post_title}", 0, 0, 0, false, '', false);
        
        $pdf->SetTextColor(0, 0, 0);
        $pdf->writeHTMLCell(0, 2, 220, $y, "1", 0, 0, 0, false, '', false);
        $i++;
    }

    if($cena_konstrukce !== '0' || $cena_konstrukce === ''){
        $pdf->writeHTMLCell(0, 2, 34.5, $y + 12, "Střecha - konstrukce", 0, 0, 0, false, '', false);   
        $pdf->writeHTMLCell(0, 2, 220, $y + 12, "1", 0, 0, 0, false, '', false);
    }

    $pdf->writeHTMLCell(0, 2, 34.5, $y + 24, "Dotace celkem: {$dotace_title}", 0, 0, 0, false, '', false);
    $pdf->writeHTMLCell(0, 2, 220, $y + 24, "1", 0, 0, 0, false, '', false);

    $pdf->SetFont('exo2b', '', 12);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->writeHTMLCell(210, 0, 205, 258.7, "{$cena_bez_dph} Kč", 0, 0, 0, true, '', false);
    $pdf->writeHTMLCell(210, 0, 205, 268.5, "{$dph} Kč", 0, 0, 0, true, '', false);
    $pdf->writeHTMLCell(210, 0, 205, 278.5, "{$cena_celkem} Kč", 0, 0, 0, true, '', false);
    $pdf->writeHTMLCell(210, 0, 205, 288.5, "-{$dotace_price} Kč", 0, 0, 0, true, '', false);
    $pdf->SetFont('exo2b', '', 13);
    $pdf->writeHTMLCell(210, 0, 203, 302, "{$final_price} Kč", 0, 0, 0, true, '', false);
    
    $tplidx = $pdf->importPage(3);
    $pdf->AddPage();
    $pdf->useTemplate($tplidx);

    $pdf->SetTextColor(17, 115,160);
    $pdf->SetFont('exo2b', '', 14);
    $pdf->writeHTMLCell(210, 0, 20, 48, "Solární panely: {$panel_vyrobce_name} - {$panel_name}", 0, 0, 0, true, '', false);

    $pdf->SetFont('exo2b', '', 12);
    $pdf->writeHTMLCell(210, 0, 20, 58, "{$panel_popis}", 0, 0, 0, true, '', false);

    $pdf->SetFont('exo2b', '', 14); 
    $pdf->writeHTMLCell(210, 0, 20, 100, "Střídač: {$stridac_vyrobce_name} - {$stridac_name}", 0, 0, 0, true, '', false);

    $pdf->SetFont('exo2b', '', 12);
    $pdf->writeHTMLCell(210, 0, 20, 110, "{$stridac_popis}", 0, 0, 0, true, '', false);
    

    $pdf->SetFont('exo2b', '', 14);
    $pdf->writeHTMLCell(210, 0, 20, 152, "Baterie: {$baterie_vyrobce_name} - {$baterie_name}", 0, 0, 0, true, '', false);

    $pdf->SetFont('exo2b', '', 12);
    $pdf->writeHTMLCell(210, 0, 20, 162, "{$baterie_popis}", 0, 0, 0, true, '', false);

    

    $tplidx = $pdf->importPage(4);
    $pdf->AddPage();
    $pdf->useTemplate($tplidx);

    $tplidx = $pdf->importPage(5);
    $pdf->AddPage();
    $pdf->useTemplate($tplidx);
    
    ob_end_clean();
    $file_name = $name.'.pdf';
    $pdf->Output($file_name, 'D');    
    exit;
}

function show_contract_customer_number_for_each_page($pdf, $customer_number) {
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('exo2b', '', 8);
    $pdf->writeHTMLCell(30, 20, 138, 15, "SMLOUVA O DÍLO Č.", 0, 0, 0, true, '', false);

    $pdf->SetTextColor(17, 115,160);
    $pdf->SetFont('exo2b', '', 8);
    $pdf->writeHTMLCell(30, 20, 165, 15, "{$customer_number}", 0, 0, 0, true, '', false);
}

add_action('admin_post_download_contrac_pdf_action', 'download_contrac_pdf');
add_action('admin_post_nopriv_download_contrac_pdf_action', 'download_contrac_pdf');
function download_contrac_pdf() {
    $post_id = $_POST['selected_contrac_post_id'];
    $name = get_post_meta($post_id, 'zakaznik', true);
    $datetime = get_post_meta($post_id, 'datum', true);
    if (empty($datetime))
        $datetime = get_the_date('Y-m-d H:i:s', $post_id);
    $email = get_post_meta($post_id, 'e-mail', true);
    $telefon = get_post_meta($post_id, 'telefon', true);
    $address = get_post_meta($post_id, 'adresa_instalace', true);
    list($date, $time) = explode(" ", $datetime);
    $date = str_replace('-', '', $date);
    $customer_number = $date . $post_id;

    require_once WP_CONTENT_DIR . '/plugins/tecnickcom/tcpdf/tcpdf.php';
    require_once WP_CONTENT_DIR . '/plugins/setasign/fpdi/src/autoload.php';
    $pdf = new \setasign\Fpdi\Tcpdf\Fpdi('P','mm',array(218,320));
    $pagecount = $pdf->setSourceFile('template2.pdf');

    $tplidx = $pdf->importPage(1);
    $pdf->AddPage();
    $pdf->useTemplate($tplidx);

    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('exo2b', '', 16);
    $pdf->writeHTMLCell(100, 20, 24, 90, "Č. {$customer_number}", 0, 0, 0, true, '', false);

    $pdf->SetTextColor(17, 115, 160);
    $pdf->SetFont('exo2b', '', 14);
    $pdf->writeHTMLCell(100, 20, 24, 208, $name, 0, 0, 0, true, '', false);

    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('exo2b', '', 10);
    $pdf->writeHTMLCell(100, 20, 34, 221, $address, 0, 0, 0, true, '', false);

    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('exo2b', '', 10);
    $pdf->writeHTMLCell(100, 20, 44, 231, $email, 0, 0, 0, true, '', false);

    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('exo2b', '', 10);
    $pdf->writeHTMLCell(100, 20, 32, 241, $telefon, 0, 0, 0, true, '', false);

    $tplidx = $pdf->importPage(2);
    $pdf->AddPage();
    $pdf->useTemplate($tplidx);
    show_contract_customer_number_for_each_page($pdf, $customer_number);

    $tplidx = $pdf->importPage(3);
    $pdf->AddPage();
    $pdf->useTemplate($tplidx);
    show_contract_customer_number_for_each_page($pdf, $customer_number);

    $tplidx = $pdf->importPage(4);
    $pdf->AddPage();
    $pdf->useTemplate($tplidx);
    show_contract_customer_number_for_each_page($pdf, $customer_number);

    $tplidx = $pdf->importPage(5);
    $pdf->AddPage();
    $pdf->useTemplate($tplidx);
    show_contract_customer_number_for_each_page($pdf, $customer_number);

    $tplidx = $pdf->importPage(6);
    $pdf->AddPage();
    $pdf->useTemplate($tplidx);
    show_contract_customer_number_for_each_page($pdf, $customer_number);

    $tplidx = $pdf->importPage(7);
    $pdf->AddPage();
    $pdf->useTemplate($tplidx);
    show_contract_customer_number_for_each_page($pdf, $customer_number);

    $tplidx = $pdf->importPage(8);
    $pdf->AddPage();
    $pdf->useTemplate($tplidx);
    show_contract_customer_number_for_each_page($pdf, $customer_number);
    
    $tplidx = $pdf->importPage(9);
    $pdf->AddPage();
    $pdf->useTemplate($tplidx);
    show_contract_customer_number_for_each_page($pdf, $customer_number);

    $tplidx = $pdf->importPage(10);
    $pdf->AddPage();
    $pdf->useTemplate($tplidx);
    show_contract_customer_number_for_each_page($pdf, $customer_number);

    $tplidx = $pdf->importPage(11);
    $pdf->AddPage();
    $pdf->useTemplate($tplidx);
    show_contract_customer_number_for_each_page($pdf, $customer_number);

    $tplidx = $pdf->importPage(12);
    $pdf->AddPage();
    $pdf->useTemplate($tplidx);
    show_contract_customer_number_for_each_page($pdf, $customer_number);

    $tplidx = $pdf->importPage(13);
    $pdf->AddPage();
    $pdf->useTemplate($tplidx);
    show_contract_customer_number_for_each_page($pdf, $customer_number);

    $tplidx = $pdf->importPage(14);
    $pdf->AddPage();
    $pdf->useTemplate($tplidx);
    show_contract_customer_number_for_each_page($pdf, $customer_number);

    $tplidx = $pdf->importPage(15);
    $pdf->AddPage();
    $pdf->useTemplate($tplidx);
    show_contract_customer_number_for_each_page($pdf, $customer_number);

    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('exo2b', '', 10);
    $pdf->writeHTMLCell(100, 20, 117, 136, "Č. {$customer_number}", 0, 0, 0, true, '', false);

    ob_end_clean();
    $file_name = $name . '_Smlouva_o_dilo_č. ' . $customer_number . '.pdf';
    $pdf->Output($file_name, 'D');
    exit;
}

$kraj_arr = [
    ['wrong' => 'Hlavn msto Praha', 'right' => 'Hlavní město Praha'],       ['wrong' => 'Jihoesk kraj', 'right' => 'Jihočeský kraj'],               ['wrong' => 'Jihomoravsk kraj', 'right' => 'Jihomoravský kraj'],
    ['wrong' => 'Karlovarsk kraj', 'right' => 'Karlovarský kraj'],          ['wrong' => 'Krlovehradeck kraj', 'right' => 'Královehradecký kraj'],   ['wrong' => 'Libereck kraj', 'right' => 'Liberecký kraj'],
    ['wrong' => 'Moravskoslezsk kraj', 'right' => 'Moravskoslezský kraj'],  ['wrong' => 'Olomouck kraj', 'right' => 'Olomoucký kraj'],              ['wrong' => 'Pardubick kraj', 'right' => 'Pardubický kraj'],
    ['wrong' => 'Plzesk kraj', 'right' => 'Plzeňský kraj'],                 ['wrong' => 'Stedoesk kraj', 'right' => 'Středočeský kraj'],            ['wrong' => 'steck kraj', 'right' => 'Ústecký kraj'],
    ['wrong' => 'Vysoina', 'right' => 'Vysočina'],                          ['wrong' => 'Zlnsk kraj', 'right' => 'Zlínský kraj'],
];

$druh_nemovitosti_arr = [
    ['wrong' => 'rodinny_dum', 'right' => 'Rodinný dům'], ['wrong' => 'chata', 'right' => 'Chata'],
    ['wrong' => 'firma', 'right' => 'Firma'], ['wrong' => 'jine', 'right' => 'Jiné']
];

$zpusob_ohrevu_arr = [
    ['wrong' => 'play', 'right' => 'Plyn'], ['wrong' => 'elektrina', 'right' => 'Elektřina'], ['wrong' => 'termika', 'right' => 'Termika'],
    ['wrong' => 'tuha_paliva', 'right' => 'Tuhá paliva'], ['wrong' => 'kombinace', 'right' => 'Kombinace'],
];

$material_arr = [
    ['wrong' => 'taska', 'right' => 'Taška'], ['wrong' => 'sindel', 'right' => 'Šindel'], ['wrong' => 'plech_falcovy', 'right' => 'Plech - falcový'],
    ['wrong' => 'plech_sablony', 'right' => 'Plech - šablony'], ['wrong' => 'eternit_vlnity', 'right' => 'Eternit - vlnitý'], ['wrong' => 'eternit_sablony', 'right' => 'Eternit - šablony'],
    ['wrong' => 'gembrit', 'right' => 'Gembrit'], ['wrong' => 'rsb', 'right' => 'Rovná střecha betonová (překlady, armování)'], ['wrong' => 'rsn', 'right' => 'Rovná střecha nenosná'],
];

$umisteni_arr = [
    ['wrong' => 'rd', 'right' => 'Rodinný dům'], ['wrong' => 'garaz', 'right' => 'Garáž'],
    ['wrong' => 'pergola', 'right' => 'Pergola'], ['wrong' => 'jine', 'right' => 'Jiné']
];

$formular_byl_vyplnen_za_pomoci_telefonicke_podpory_arr = [
    ['ne' => 'Ne', 'right' => 'Rodinný dům'], ['wrong' => 'ano', 'right' => 'Ano'],
];

$o_společnosti_genius_fve_jsem_se_dozvedel_pres_arr = [
    ['wrong' => 'doporuceni', 'right' => 'Doporučení'], ['wrong' => 'google', 'right' => 'Google'],
    ['wrong' => 'seznam', 'right' => 'Seznam'], ['wrong' => 'tel_nabidka', 'right' => 'Telefonickou nabídku']
];

add_action('admin_post_download_zakaznic_pdf_action', 'download_zakaznic_pdf');
add_action('admin_post_nopriv_download_zakaznic_pdf_action', 'download_zakaznic_pdf');
function download_zakaznic_pdf() {
    // $nabidky_post_id = $_POST['selected_zakaznic_post_id'];
    // $name = get_post_meta($nabidky_post_id, 'zakaznik', true);
    // $post = get_page_by_title($name, OBJECT, 'zakaznik');
    // $post_id = $post->ID;
    // $formular_post_id = get_post_meta($post_id, 'formular', true);
    // $email = get_post_meta($post_id, 'e-mail', true);
    // $telefon = get_post_meta($post_id, 'telefon', true);
    // $title = explode(' ', $name, 2);
    // $kraj = get_post_meta($formular_post_id, '_field_63', true);
    // for ($i = 1; $i <= count($GLOBALS["kraj_arr"]); $i++) {
    //     if ($kraj == $GLOBALS["kraj_arr"][$i - 1]["wrong"]) {
    //         $kraj = $GLOBALS["kraj_arr"][$i - 1]["right"];
    //         break;
    //     };
    // }
    // $adresa_realizace = get_post_meta($formular_post_id, '_field_12', true);
    // $druh_nemovitosti = get_post_meta($formular_post_id, '_field_13', true);
    // for ($i = 1; $i <= count($GLOBALS["druh_nemovitosti_arr"]); $i++) {
    //     if ($druh_nemovitosti == $GLOBALS["druh_nemovitosti_arr"][$i - 1]["wrong"]) {
    //         $druh_nemovitosti = $GLOBALS["druh_nemovitosti_arr"][$i - 1]["right"];
    //         break;
    //     };
    // }
    // $spotreba_domu1 = get_post_meta($formular_post_id, '_field_15', true);
    // $spotreba_domu2 = get_post_meta($formular_post_id, '_field_16', true);
    // $zpusob_vytapeni = unserialize(get_post_meta($formular_post_id, '_field_34', true));
    // $tc_kwh = get_post_meta($formular_post_id, '_field_35', true);
    // $elektrokotel_kwh = get_post_meta($formular_post_id, '_field_37', true);
    // $pevna_paliva = get_post_meta($formular_post_id, '_field_38', true);
    // $primotopy_kWh = get_post_meta($formular_post_id, '_field_39', true);
    // $jine = get_post_meta($formular_post_id, '_field_40', true);
    // $zpusob_ohrevu = get_post_meta($formular_post_id, '_field_30', true);
    // for ($i = 1; $i <= count($GLOBALS["zpusob_ohrevu_arr"]); $i++) {
    //     if ($zpusob_ohrevu == $GLOBALS["zpusob_ohrevu_arr"][$i - 1]["wrong"]) {
    //         $zpusob_ohrevu = $GLOBALS["zpusob_ohrevu_arr"][$i - 1]["right"];
    //         break;
    //     };
    // }
    // $bojler = get_post_meta($formular_post_id, '_field_41', true);
    // $kombinace_uvedte_nize = get_post_meta($formular_post_id, '_field_42', true);
    // $material = get_post_meta($formular_post_id, '_field_21', true);
    // for ($i = 1; $i <= count($GLOBALS["material_arr"]); $i++) {
    //     if ($material == $GLOBALS["material_arr"][$i - 1]["wrong"]) {
    //         $material = $GLOBALS["material_arr"][$i - 1]["right"];
    //         break;
    //     };
    // }
    // $umisteni = get_post_meta($formular_post_id, '_field_22', true);
    // for ($i = 1; $i <= count($GLOBALS["umisteni_arr"]); $i++) {
    //     if ($umisteni == $GLOBALS["umisteni_arr"][$i - 1]["wrong"]) {
    //         $umisteni = $GLOBALS["umisteni_arr"][$i - 1]["right"];
    //         break;
    //     };
    // }
    // $rozmer_strechy_pro_panely = get_post_meta($formular_post_id, '_field_24', true);
    // $orientace = get_post_meta($formular_post_id, '_field_26', true);
    // $klimatizace = get_post_meta($formular_post_id, '_field_44', true);
    // $akvaria_teraria = get_post_meta($formular_post_id, '_field_45', true);
    // $virivka = get_post_meta($formular_post_id, '_field_46', true);
    // $sauna = get_post_meta($formular_post_id, '_field_47', true);
    // $bazen_s_filtraci = get_post_meta($formular_post_id, '_field_48', true);
    // $bazen_s_ohrevem_a_filtraci = get_post_meta($formular_post_id, '_field_49', true);
    // $protiproud = get_post_meta($formular_post_id, '_field_50', true);
    // $rekuperace = get_post_meta($formular_post_id, '_field_51', true);
    // $tocive_motory_3f = get_post_meta($formular_post_id, '_field_52', true);
    // $doplnkove_pozadavky_na_fve = get_post_meta($formular_post_id, '_field_23', true);
    // $formular_byl_vyplnen_za_pomoci_telefonicke_podpory = get_post_meta($formular_post_id, '_field_57', true);
    // for ($i = 1; $i <= count($GLOBALS["formular_byl_vyplnen_za_pomoci_telefonicke_podpory_arr"]); $i++) {
    //     if ($formular_byl_vyplnen_za_pomoci_telefonicke_podpory == $GLOBALS["formular_byl_vyplnen_za_pomoci_telefonicke_podpory_arr"][$i - 1]["wrong"]) {
    //         $formular_byl_vyplnen_za_pomoci_telefonicke_podpory = $GLOBALS["formular_byl_vyplnen_za_pomoci_telefonicke_podpory_arr"][$i - 1]["right"];
    //         break;
    //     };
    // }
    // $o_společnosti_genius_fve_jsem_se_dozvedel_pres = get_post_meta($formular_post_id, '_field_58', true);
    // for ($i = 1; $i <= count($GLOBALS["o_společnosti_genius_fve_jsem_se_dozvedel_pres_arr"]); $i++) {
    //     if ($o_společnosti_genius_fve_jsem_se_dozvedel_pres == $GLOBALS["o_společnosti_genius_fve_jsem_se_dozvedel_pres_arr"][$i - 1]["wrong"]) {
    //         $o_společnosti_genius_fve_jsem_se_dozvedel_pres = $GLOBALS["o_společnosti_genius_fve_jsem_se_dozvedel_pres_arr"][$i - 1]["right"];
    //         break;
    //     };
    // }

    // $datetime = get_post_meta($post_id, 'datum', true);
    // $balicek_id = get_post_meta($post_id, 'vyberte_balicek', true);
    // list($date, $time) = explode(" ", $datetime);
    // $date_pdf = str_replace('-', '.', $date);
    // $date = str_replace('-', '', $date);
    // $smlouva_number = $date.$post_id;

    // require_once WP_CONTENT_DIR . '/plugins/tecnickcom/tcpdf/tcpdf.php';
    // require_once WP_CONTENT_DIR . '/plugins/setasign/fpdi/src/autoload.php';
    // $pdf = new \setasign\Fpdi\Tcpdf\Fpdi('P','mm',array(250,350));

    // $pdf->setCellHeightRatio(2);

    // $pdf->AddPage();
    // $pdf->SetFont('exo2b', '', 11);
    // $content = <<<EOD
    // <table cellpadding="2" cellspacing="5">
    //     <tr nobr="true">
    //         <td colspan="3">Jméno: {$title[0]}</td>
    //         <td colspan="3">Příjmení: {$title[1]}</td>
    //     </tr>
    //     <tr nobr="true">
    //         <td colspan="3">Email: {$email}</td>
    //         <td colspan="3">Telefon: {$telefon}</td>
    //     </tr>
    //     <tr nobr="true">
    //         <td colspan="6">Kraj: {$kraj}</td>
    //     </tr>
    //     <tr nobr="true">
    //         <td colspan="3">Přesná adresa instalace: {$adresa_realizace}</td>
    //         <td colspan="3">Druh nemovitosti: {$druh_nemovitosti}</td>
    //     </tr>
    // </table>
    // <hr style="border-top: dotted 1px;" />
    // <p style="color: #1173a0">SPOTŘEBA DOMU</p>
    // <table cellpadding="2" cellspacing="5">
    //     <tr nobr="true">
    //         <td colspan="3">Spotřeba domu [MWh/rok]: {$spotreba_domu1}</td>
    //         <td colspan="3">Spotřeba domu [Kč/měsíc]: {$spotreba_domu2}</td>
    //     </tr>
    // </table>
    // <hr style="border-top: dotted 1px;" />
    // <p style="color: #1173a0">TOPENÍ</p>
    // <table cellpadding="2" cellspacing="5">
    //     <tr nobr="true">
    //         <td colspan="6">Způsob vytápění: {$zpusob_vytapeni}</td>
    //     </tr>
    //     <tr nobr="true">
    //         <td colspan="2">TČ [kWh]: {$tc_kwh}</td>
    //         <td colspan="2">Elektrokotel [kWh]: {$elektrokotel_kwh}</td>
    //         <td colspan="2">Pevná paliva: {$pevna_paliva}</td>
    //     </tr>
    //     <tr nobr="true">
    //         <td colspan="2">Přímotopy [kWh]: {$primotopy_kWh}</td>
    //         <td colspan="2">Jiné: {$jine}</td>
    //     </tr>
    // </table>
    // <hr style="border-top: dotted 1px;" />
    // <p style="color: #1173a0">TEPLÁ VODA</p>
    // <table cellpadding="2" cellspacing="5">
    //     <tr nobr="true">
    //         <td colspan="6">Způsob ohřevu: {$zpusob_ohrevu}</td>
    //     </tr>
    //     <tr nobr="true">
    //         <td colspan="6">Bojler: {$bojler}</td>
    //     </tr>
    //     <tr nobr="true">
    //         <td colspan="6">Kombinace? Uveďte níže: {$kombinace_uvedte_nize}</td>
    //     </tr>
    // </table>
    // <hr style="border-top: dotted 1px;" />
    // <p style="color: #1173a0">STŘECHA</p>
    // <table cellpadding="2" cellspacing="5">
    //     <tr nobr="true">
    //         <td colspan="3">Materiál: {$material}</td>
    //         <td colspan="3">Umístění: {$umisteni}</td>
    //     </tr>
    //     <tr nobr="true">
    //         <td colspan="3">Rozměr střechy pro panely [m x m]: {$rozmer_strechy_pro_panely}</td>
    //         <td colspan="3">Orientace (ve stupních, popř. J, JZ, JV, V, Z): {$orientace}</td>
    //     </tr>
    // </table>
    // <hr style="border-top: dotted 1px;" />
    // <p style="color: #1173a0">SPOTŘEBIČE</p>
    // <table cellpadding="2" cellspacing="5">
    //     <tr nobr="true">
    //         <td colspan="2">Klimatizace: {$klimatizace}</td>
    //         <td colspan="2">Akvária, terária: {$akvaria_teraria}</td>
    //         <td colspan="2">Vířivka: {$virivka}</td>
    //     </tr>
    //     <tr nobr="true">
    //         <td colspan="2">Sauna: {$sauna}</td>
    //         <td colspan="2">Bazén s filtrací: {$bazen_s_filtraci}</td>
    //         <td colspan="2">Bazén s ohřevem a filtrací: {$bazen_s_ohrevem_a_filtraci}</td>
    //     </tr>
    //     <tr nobr="true">
    //         <td colspan="2">Protiproud: {$protiproud}</td>
    //         <td colspan="2">Rekuperace: {$rekuperace}</td>
    //         <td colspan="2">Točivé motory 3F: {$tocive_motory_3f}</td>
    //     </tr>
    // </table>
    // <hr style="border-top: dotted 1px;" />
    // <table cellpadding="2" cellspacing="5">
    //     <tr nobr="true">
    //         <td colspan="6">Doplňkové požadavky na FVE: {$doplnkove_pozadavky_na_fve}</td>
    //     </tr>
    //     <tr nobr="true">
    //         <td colspan="3">Formulář byl vyplněn za pomoci telefonické podpory: {$formular_byl_vyplnen_za_pomoci_telefonicke_podpory}</td>
    //         <td colspan="3">O společnosti Genius FVE jsem se dozvěděl přes: {$o_společnosti_genius_fve_jsem_se_dozvedel_pres}</td>
    //     </tr>
    // </table>
    // EOD;
    // $pdf->writeHTML($content);

    // ob_end_clean();
    // $file_name = $name.'_zakaznic.pdf';
    // $pdf->Output($file_name, 'D');
    // exit;

    $nabidky_post_id = $_POST['selected_zakaznic_post_id'];
    $name = get_post_meta($nabidky_post_id, 'zakaznik', true);
    $post = get_page_by_title($name, OBJECT, 'zakaznik');
    $post_id = $post->ID;
    $obhlidka_post_id = get_post_meta($post_id, 'obhlidka', true);

    global $wpdb; 
    $metaTable = $wpdb->prefix.'frmt_form_entry_meta';

    $select = $text = $radio = $textarea = $number = $date = [];
    for ($i = 1; $i <= 13; $i++)
        $select[$i] = $wpdb->get_results ( "SELECT meta_value FROM $metaTable WHERE `entry_id` = $obhlidka_post_id AND `meta_key` = 'select-" . $i . "'" )[0]->meta_value;
    for ($i = 1; $i <= 18; $i++)
        $text[$i] = $wpdb->get_results ( "SELECT meta_value FROM $metaTable WHERE `entry_id` = $obhlidka_post_id AND `meta_key` = 'text-" . $i . "'" )[0]->meta_value;
    $date[1] = $wpdb->get_results ( "SELECT meta_value FROM $metaTable WHERE `entry_id` = $obhlidka_post_id AND `meta_key` = 'date-1'" )[0]->meta_value;
    if ($date[1]) $date[1] = date('Y-m-d', strtotime($date[1]));
    $radio[1] = $wpdb->get_results ( "SELECT meta_value FROM $metaTable WHERE `entry_id` = $obhlidka_post_id AND `meta_key` = 'radio-1'" )[0]->meta_value;
    for ($i = 1; $i <= 3; $i++)
        $textarea[$i] = $wpdb->get_results ( "SELECT meta_value FROM $metaTable WHERE `entry_id` = $obhlidka_post_id AND `meta_key` = 'textarea-" . $i . "'" )[0]->meta_value;
    for ($i = 1; $i <= 3; $i++)
        $number[$i] = $wpdb->get_results ( "SELECT meta_value FROM $metaTable WHERE `entry_id` = $obhlidka_post_id AND `meta_key` = 'number-" . $i . "'" )[0]->meta_value;

    $entry = Forminator_API::get_entry( 881, $obhlidka_post_id );
    $upload = $upload_url = [];
    for ($i = 1; $i <= 15; $i++) {
        $upload[$i] = $entry->meta_data['upload-' . $i]['value'];
        if (!empty($upload[$i])) {
            for ($j = 0; $j < count($upload[$i]['file']['file_url']); $j++) {
                $upload_url[$i][$j] = $upload[$i]['file']['file_url'][$j];
            }
        }
    }

    $datetime = get_post_meta($nabidky_post_id, 'datum', true);
    if (empty($datetime))
        $datetime = get_the_date('Y-m-d H:i:s', $nabidky_post_id);
    list($date1, $time) = explode(" ", $datetime);
    $date1 = str_replace('-', '', $date1);
    $smlouva_number = $date1 . $nabidky_post_id;

    require_once WP_CONTENT_DIR . '/plugins/tecnickcom/tcpdf/tcpdf.php';
    require_once WP_CONTENT_DIR . '/plugins/setasign/fpdi/src/autoload.php';
    $pdf = new \setasign\Fpdi\Tcpdf\Fpdi('P','mm',array(250,350));

    $pdf->setCellHeightRatio(2);

    $pdf->AddPage();
    $pdf->SetFont('exo2b', '', 11);
    $content = <<<EOD
    <p> </p>
    <table cellpadding="2" cellspacing="5">
        <tr nobr="true">
            <td colspan="5">
                <img src="https://geniusfve.cz/wp-content/uploads/2022/03/pdf_logo.png" width="120" height="35" border="0" />
            </td>
            <td colspan="1">{$smlouva_number}</td>
        </tr>
    </table>
    <hr>
    <table cellpadding="2" cellspacing="5">
        <tr nobr="true">
            <td colspan="6">Datum obhlídky: {$date[1]}</td>
        </tr>
    </table>
    <p style="color: #1173a0">ZÁKAZNÍK</p>
    <table cellpadding="2" cellspacing="5">
        <tr nobr="true">
            <td colspan="6">Kontakt na zákazníka: {$text[16]}</td>
        </tr>
        <tr nobr="true">
            <td colspan="6">Jméno a příjmení: {$text[17]}</td>
        </tr>
    </table>
    <p style="color: #1173a0">STŘECHA</p>
    <table cellpadding="2" cellspacing="5">
        <tr nobr="true">
            <td colspan="6">Typ konstrukce: {$select[1]}</td>
        </tr>
        <tr nobr="true">
            <td colspan="2">Orientace umístění: {$text[1]}</td>
            <td colspan="2">Orientace umístění: {$text[2]}</td>
            <td colspan="2">Orientace umístění: {$text[3]}</td>
        </tr>
        <tr nobr="true">
            <td colspan="6">Délka svodu ze střechy k technologii (odhad v m): {$text[4]}</td>
        </tr>
        <tr nobr="true">
            <td colspan="6">Trasa: {$select[2]}</td>
        </tr>
        <tr nobr="true">
            <td colspan="2">Svod: {$select[3]}</td>
            <td colspan="2">Délka: {$text[5]}</td>
            <td colspan="2">Barva: {$text[6]}</td>
        </tr>
        <tr nobr="true">
            <td colspan="6">Formulář byl vyplněn za pomoci telefonické podpory: {$radio[1]}</td>
        </tr>
        <tr nobr="true">
            <td colspan="2">
                <p>Foto krovů:</p>
                <img src="{$upload_url[1][0]}" width="100" height="100" border="0" />
            </td>
            <td colspan="2">
                <p>Detailní foto krytiny: </p>
                <img src="{$upload_url[2][0]}" width="100" height="100" border="0" />
            </td>
            <td colspan="2">
                <p>Foto střech pro umístění panelů: </p>
                <img src="{$upload_url[3][0]}" width="100" height="100" border="0" />
            </td>
        </tr>
    </table>
    <p style="color: #1173a0">ELEKTRO</p>
    <table cellpadding="2" cellspacing="5">
        <tr nobr="true">
            <td colspan="3">HJ: {$text[7]}</td>
            <td colspan="3">Ampéráž: {$text[8]}</td>
        </tr>
        <tr nobr="true">
            <td colspan="3">kV: {$text[9]}</td>
            <td colspan="3">Výměna nutná: {$select[4]}</td>
        </tr>
        <tr nobr="true">
            <td colspan="6">Místo pro kříž FVE: {$select[5]}</td>
        </tr>
        <tr nobr="true">
            <td colspan="6">
                <p>Foto otevřeného elměr rozvaděče celého, detail HJ:</p>
                <img src="{$upload_url[4][0]}" width="100" height="100" border="0" />
            </td>
        </tr>
        <tr nobr="true">
            <td colspan="6">Umístění DR: {$text[10]}</td>
        </tr>
        <tr nobr="true">
            <td colspan="6">Umístění technologie: {$text[11]}</td>
        </tr>
        <tr nobr="true">
            <td colspan="6">Délka technologické trasy (odhad v m): {$text[12]}</td>
        </tr>
        <tr nobr="true">
            <td colspan="6">Wattrouter: {$select[6]}</td>
        </tr>
        <tr nobr="true">
            <td colspan="3">Protihořlavá deska do rozvaděče: {$select[7]}</td>
            <td colspan="3">Odhad v m²: {$text[13]}</td>
        </tr>
        <tr nobr="true">
            <td colspan="2">Trasa zasekat: {$select[8]}</td>
            <td colspan="2">Trasa zasekat: {$select[9]}</td>
            <td colspan="2">Délka v m: {$text[14]}</td>
        </tr>
        <tr nobr="true">
            <td colspan="6">Speciální požadavky: {$textarea[1]}</td>
        </tr>
        <tr nobr="true">
            <td colspan="6">Stavební připravenost: {$textarea[2]}</td>
        </tr>
        <tr nobr="true">
            <td colspan="6">Shrnutí prohlídky: {$textarea[3]}</td>
        </tr>
        <tr nobr="true">
            <td colspan="3">
                <p>FOTO DOMU ZE VŠECH SVĚTOVÝCH STRAN: </p>
                <img src="{$upload_url[5][0]}" width="100" height="100" border="0" />
            </td>
            <td colspan="3">
                <p>FOTO DOMU ZE VŠECH SVĚTOVÝCH STRAN: </p>
                <img src="{$upload_url[6][0]}" width="100" height="100" border="0" />
            </td>
        </tr>
        <tr nobr="true">
            <td colspan="3">
                <p>FOTO DOMU ZE VŠECH SVĚTOVÝCH STRAN: </p>
                <img src="{$upload_url[7][0]}" width="100" height="100" border="0" />
            </td>
            <td colspan="3">
                <p>FOTO DOMU ZE VŠECH SVĚTOVÝCH STRAN: </p>
                <img src="{$upload_url[8][0]}" width="100" height="100" border="0" />
            </td>
        </tr>
        <tr nobr="true">
            <td colspan="2">
                <p>FOTO DOMU - ČÍSLO POPISNÉ: </p>
                <img src="{$upload_url[9][0]}" width="100" height="100" border="0" />
            </td>
            <td colspan="2">
                <p>FOTO ZDROJE TUV: </p>
                <img src="{$upload_url[10][0]}" width="100" height="100" border="0" />
            </td>
            <td colspan="2">
                <p>FOTO ZDROJE TEPLA: </p>
                <img src="{$upload_url[11][0]}" width="100" height="100" border="0" />
            </td>
        </tr>
        <tr nobr="true">
            <td colspan="2">
                <p>VYÚČTOVACÍ FAKTURA ZA EE: </p>
                <img src="{$upload_url[12][0]}" width="100" height="100" border="0" />
            </td>
            <td colspan="2">
                <p>VYÚČTOVACÍ FAKTURA ZA PLYN: </p>
                <img src="{$upload_url[13][0]}" width="100" height="100" border="0" />
            </td>
            <td colspan="2">POČET OSOB V DOMÁCNOSTI: {$number[1]}</td>
        </tr>
        <tr nobr="true">
            <td colspan="2">VELIKOST VYTÁPĚNÉ PLOCHY [V M²]: {$number[2]}</td>
            <td colspan="2">STÁŘÍ STŘEŠNÍ KRYTINY [V LETECH]: {$number[3]}</td>
            <td colspan="2">EXISTENCE NABÍJEČKY ELEKTROMOBILŮ: {$select[13]}</td>
        </tr>
        <tr nobr="true">
            <td colspan="2">EXISTENCE KLIMATIZACE: {$select[11]}</td>
            <td colspan="2">EXISTENCE BAZÉNU: {$select[12]}</td>
            <td colspan="2">TYP STŘEŠNÍ KRYTINY: {$text[18]}</td>
        </tr>
        <tr nobr="true">
            <td colspan="3">
                <p>VYÚČTOVACÍ FAKTURA ZA EE: </p>
                <img src="{$upload_url[14][0]}" width="100" height="100" border="0" />
            </td>
            <td colspan="3">
                <p>VYÚČTOVACÍ FAKTURA ZA PLYN: </p>
                <img src="{$upload_url[15][0]}" width="100" height="100" border="0" />
            </td>
        </tr>
    </table>
    EOD;
    $pdf->writeHTML($content);

    ob_end_clean();
    $file_name = $name.'_zakaznic.pdf';
    $pdf->Output($file_name, 'D');
    exit;
}

add_action('admin_post_download_technical_pdf_action', 'download_technical_pdf');
add_action('admin_post_nopriv_download_technical_pdf_action', 'download_technical_pdf');
function download_technical_pdf() {
    $post_id = $_POST['selected_technical_post_id'];
    $name = get_post_meta($post_id, 'zakaznik', true);
    $datetime = get_post_meta($post_id, 'datum', true);
    if (empty($datetime))
        $datetime = get_the_date('Y-m-d H:i:s', $post_id);
    $balicek_id = get_post_meta($post_id, 'vyberte_balicek', true);
    list($date, $time) = explode(" ", $datetime);
    $date_pdf = str_replace('-', '.', $date);
    $date = str_replace('-', '', $date);
    $smlouva_number = $date.$post_id;

    $panel_n = get_post_meta( $post_id, 'pocet_panelu', true);
    $baterie_n = get_post_meta( $post_id, 'pocet_baterii', true);
    $stridac_n = get_post_meta( $post_id, 'pocet_stridacu', true);

    $panel_id = get_post_meta($balicek_id, 'panel', true);
    $baterie_id = get_post_meta($balicek_id, 'baterie', true);
    $stridac_id = get_post_meta($balicek_id, 'stridac', true);
    
    $panel = get_field('panel', $balicek_id);
    $panel_name = $panel->post_title;
    $panel_vyrobce =  get_field('vyrobce', $panel_id);
    $panel_vyrobce_name = $panel_vyrobce->post_title;
    $panel_pocet = get_post_meta($post_id, 'pocet_panelu', true);
    $panel_popis = get_post_meta($panel_id, 'popis', true);
    $panel_svt = get_post_meta($panel_id, 'svt', true);
    $panel_cena_nakup = get_post_meta($panel_id, 'cena_nakup', true);
    $panel_cena_celkem = (int)$panel_cena_nakup * (int)$panel_n;

    $baterie = get_field('baterie', $balicek_id);
    $baterie_name = $baterie->post_title;
    $baterie_vyrobce =  get_field('vyrobce', $baterie_id);
    $baterie_vyrobce_name = $baterie_vyrobce->post_title;
    $baterie_pocet = get_post_meta($post_id, 'pocet_baterii', true);
    $baterie_popis = get_post_meta($baterie_id, 'popis', true);
    $baterie_cena_nakup = get_post_meta($baterie_id, 'cena_nakup', true);
    $baterie_cena_celkem =  (int)$baterie_cena_nakup * (int)$baterie_n;
    
    $stridac = get_field('stridac', $balicek_id);
    $stridac_name = $stridac->post_title;
    $stridac_vyrobce =  get_field('vyrobce', $stridac_id);
    $stridac_vyrobce_name = $stridac_vyrobce->post_title;
    $stridac_pocet = get_post_meta($post_id, 'pocet_stridacu', true);
    $stridac_popis = get_post_meta($stridac_id, 'popis', true);
    $stridac_svt = get_post_meta($stridac_id, 'svt', true);
    $stridac_cena_nakup = get_post_meta($stridac_id, 'cena_nakup', true);
    $stridac_cena_celkem = (int)$stridac_cena_nakup * (int)$stridac_n;

    $real_price = (int)get_post_meta($post_id, 'vlastni_investice_celkem', true);
    $cena_bez_dph = $real_price / 1.21;
    $cena_bez_dph = (int)$cena_bez_dph;
    $dph = (int)$cena_bez_dph * 0.21;
    $dph = (int)$dph;
    $cena_celkem = $cena_bez_dph + $dph;

    $balicek_komponenty = get_field('komponenty', $balicek_id);

    require_once WP_CONTENT_DIR . '/plugins/tecnickcom/tcpdf/tcpdf.php';
    require_once WP_CONTENT_DIR . '/plugins/setasign/fpdi/src/autoload.php';
    $pdf = new \setasign\Fpdi\Tcpdf\Fpdi('P','mm',array(250,350));
    $pagecount = $pdf->setSourceFile('templete4.pdf');

    $tplidx = $pdf->importPage(1);
    $pdf->AddPage();
    $pdf->useTemplate($tplidx);

    $pdf->SetTextColor(0, 0 , 0);
    $pdf->SetFont('exo2b', '', 14);
    $pdf->writeHTMLCell(0, 2, 200, 18, "{$smlouva_number}", 0, 0, 0, false, '', false);
    $date = date_create($smlouva_number);

    $pdf->SetFont('exo2light', '', 10);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->writeHTMLCell(0, 2, 12, 75, "{$content}", 0, 0, 0, false, '', false);
    $pdf->SetFont('exo2light', '', 12);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->writeHTMLCell(0, 2, 120, 75, "{$panel_pocet}", 0, 0, 0, false, '', false);
    $pdf->writeHTMLCell(0, 2, 137, 75, "{$panel_svt}", 0, 0, 0, false, '', false);
    $pdf->writeHTMLCell(0, 2, 175, 75, "{$panel_cena_nakup} Kč", 0, 0, 0, false, '', false);
    $pdf->writeHTMLCell(0, 2, 210, 75, "{$panel_cena_celkem} Kč", 0, 0, 0, false, '', false);

    $pdf->SetFont('exo2light', '', 10);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->writeHTMLCell(0, 2, 12, 84, "{$stridac_vyrobce_name} - {$stridac_name}", 0, 0, 0, false, '', false);
    $pdf->SetFont('exo2light', '', 12);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->writeHTMLCell(0, 2, 120, 84, "{$stridac_pocet}", 0, 0, 0, true, '', false);
    $pdf->writeHTMLCell(0, 2, 137, 84, "{$stridac_svt}", 0, 0, 0, true, '', false);
    $pdf->writeHTMLCell(0, 2, 175, 84, "{$stridac_cena_nakup} Kč", 0, 0, 0, false, '', false);
    $pdf->writeHTMLCell(0, 2, 210, 84, "{$stridac_cena_celkem} Kč", 0, 0, 0, false, '', false);

    $pdf->SetFont('exo2light', '', 10);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->writeHTMLCell(0, 2, 12, 93, "{$baterie_vyrobce_name} - {$baterie_name}", 0, 0, 0, false, '', false);
    $pdf->SetFont('exo2light', '', 12);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->writeHTMLCell(0, 2, 120, 93, "{$baterie_pocet}", 0, 0, 0, false, '', false);
    $pdf->writeHTMLCell(0, 2, 147, 93, "-", 0, 0, 0, true, '', false);
    $pdf->writeHTMLCell(0, 2, 175, 93, "{$baterie_cena_nakup} Kč", 0, 0, 0, false, '', false);
    $pdf->writeHTMLCell(0, 2, 210, 93, "{$baterie_cena_celkem} Kč", 0, 0, 0, false, '', false);

    $i=0;
    foreach ($balicek_komponenty as $key => $value) {
        $y = $i* 9 + 102;
        $cena_nakup = get_post_meta($value->ID, 'cena_prodej', true);
        $cena_prodej = get_post_meta($value->ID, 'cena_prodej', true);

        $pdf->SetFont('exo2light', '', 10);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->writeHTMLCell(0, 2, 12, $y, "{$value->post_title}", 0, 0, 0, false, '', false);
        $pdf->SetFont('exo2light', '', 12);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->writeHTMLCell(0, 2, 120, $y, "1", 0, 0, 0, false, '', false);
        $pdf->writeHTMLCell(0, 2, 147, $y, "-", 0, 0, 0, true, '', false);
       
        $pdf->writeHTMLCell(0, 2, 175, $y, "{$cena_nakup} Kč", 0, 0, 0, true, '', false);
        $pdf->writeHTMLCell(0, 2, 210, $y, "{$cena_prodej} Kč", 0, 0, 0, true, '', false);
        $i++;
    }

    $cena_konstrukce = get_post_meta($post_id, 'cena_konstrukce', true);
    if($cena_konstrukce !== '0' || $cena_konstrukce === ''){
        if ($cena_konstrukce == "") $cena_konstrukce = 0;
        $pdf->SetFont('exo2light', '', 10);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->writeHTMLCell(0, 2, 12, $y + 9, "Střecha - konstrukce", 0, 0, 0, false, '', false);
        $pdf->SetFont('exo2light', '', 12);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->writeHTMLCell(0, 2, 120, $y + 9, "1", 0, 0, 0, false, '', false);
        $pdf->writeHTMLCell(0, 2, 147, $y + 9, "-", 0, 0, 0, true, '', false);
        $pdf->writeHTMLCell(0, 2, 175, $y + 9, "{$cena_konstrukce} Kč", 0, 0, 0, true, '', false);
        $pdf->writeHTMLCell(0, 2, 210, $y + 9, "{$cena_konstrukce} Kč", 0, 0, 0, true, '', false);
    }

    $vice_prace = get_post_meta($post_id, 'vice_prace', true);
    if($vice_prace !== '0' || $vice_prace === ''){
        if ($vice_prace == "") $vice_prace = 0;
        $pdf->SetFont('exo2light', '', 10);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->writeHTMLCell(0, 2, 12, $y + 18, "Více práce", 0, 0, 0, false, '', false);
        $pdf->SetFont('exo2light', '', 12);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->writeHTMLCell(0, 2, 120, $y + 18, "1", 0, 0, 0, false, '', false);
        $pdf->writeHTMLCell(0, 2, 147, $y + 18, "-", 0, 0, 0, true, '', false);
        $pdf->writeHTMLCell(0, 2, 175, $y + 18, "{$vice_prace} Kč", 0, 0, 0, true, '', false);
        $pdf->writeHTMLCell(0, 2, 210, $y + 18, "{$vice_prace} Kč", 0, 0, 0, true, '', false);
    }
    $pdf->SetFont('exo2b', '', 12);
    $pdf->writeHTMLCell(210, 0, 205, 217.7, "{$cena_bez_dph} Kč", 0, 0, 0, true, '', false);
    $pdf->writeHTMLCell(210, 0, 205, 225.7, "{$dph} Kč", 0, 0, 0, true, '', false);
    $pdf->writeHTMLCell(210, 0, 205, 234, "{$cena_celkem} Kč", 0, 0, 0, true, '', false);

    ob_end_clean();
    $file_name = $name.'_technical.pdf';
    $pdf->Output($file_name, 'D');    
    exit;
}
?>