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
    $cena_bez_dph = $real_price / 0.85;
    $cena_bez_dph = (int)$cena_bez_dph;
    $dph = (int)$cena_bez_dph * 0.15;
    $dph = (int)$dph;
    $cena_celkem = $cena_bez_dph - $dph;
    $final_price = $cena_celkem - (int)$dotace_price;


    $panel_id = get_post_meta($balicek_id, 'panel', true);
    $baterie_id = get_post_meta($balicek_id, 'baterie', true);
    $stridac_id = get_post_meta($balicek_id, 'stridac', true);
    
    $panel = get_field('panel', $balicek_id);
    $panel_name = $panel->post_title;
    $panel_vyrobce =  get_field('vyrobce', $panel_id);
    $panel_vyrobce_name = $panel_vyrobce->post_title;
    $panel_pocet = get_post_meta($balicek_id, 'defaultni_pocet_panelu', true);
    $panel_popis = get_post_meta($panel_id, 'popis', true);

    $baterie = get_field('baterie', $balicek_id);
    $baterie_name = $baterie->post_title;
    $baterie_vyrobce =  get_field('vyrobce', $baterie_id);
    $baterie_vyrobce_name = $baterie_vyrobce->post_title;
    $baterie_pocet = get_post_meta($balicek_id, 'defaultni_pocet_baterii', true);
    $baterie_popis = get_post_meta($baterie_id, 'popis', true);
    
    $stridac = get_field('stridac', $balicek_id);
    $stridac_name = $stridac->post_title;
    $stridac_vyrobce =  get_field('vyrobce', $stridac_id);
    $stridac_vyrobce_name = $stridac_vyrobce->post_title;
    $stridac_pocet = get_post_meta($balicek_id, 'defaultni_pocet_stridacu', true);
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


add_action('admin_post_download_contrac_pdf_action', 'download_contrac_pdf');
add_action('admin_post_nopriv_download_contrac_pdf_action', 'download_contrac_pdf');

function download_contrac_pdf() {
    $post_id = $_POST['selected_contrac_post_id'];
    $name = get_post_meta($post_id, 'zakaznik', true);

    // $address = get_post_meta($post_id, 'adresa_instalace', true);
    // $balicek_id = get_post_meta($post_id, 'vyberte_balicek', true);
    
    // $panel_id = get_post_meta($balicek_id, 'panel', true);
    // $baterie_id = get_post_meta($balicek_id, 'baterie', true);
    // $stridac_id = get_post_meta($balicek_id, 'stridac', true);
    
    // $panel = get_field('panel', $balicek_id);
    // $panel_name = $panel->post_title;
    // $panel_vyrobce =  get_field('vyrobce', $panel_id);
    // $panel_vyrobce_name = $panel_vyrobce->post_title;
    // $panel_pocet = get_post_meta($balicek_id, 'defaultni_pocet_panelu', true);
    // $panel_popis = get_post_meta($panel_id, 'popis', true);

    // $baterie = get_field('baterie', $balicek_id);
    // $baterie_name = $baterie->post_title;
    // $baterie_vyrobce =  get_field('vyrobce', $baterie_id);
    // $baterie_vyrobce_name = $baterie_vyrobce->post_title;
    // $baterie_pocet = get_post_meta($balicek_id, 'defaultni_pocet_baterii', true);
    // $baterie_popis = get_post_meta($baterie_id, 'popis', true);
    
    // $stridac = get_field('stridac', $balicek_id);
    // $stridac_name = $stridac->post_title;
    // $stridac_vyrobce =  get_field('vyrobce', $stridac_id);
    // $stridac_vyrobce_name = $stridac_vyrobce->post_title;
    // $stridac_pocet = get_post_meta($balicek_id, 'defaultni_pocet_stridacu', true);
    // $stridac_popis = get_post_meta($stridac_id, 'popis', true);

    // $balicek_ostatni = get_field('ostatni', $balicek_id);
    
    
    // $balicek = get_field('vyberte_balicek', $post_id);
    // $balicek_name = $balicek->post_title;

    // $name = iconv('utf-8', 'cp1250', $name);

    // require_once WP_CONTENT_DIR . '/plugins/tecnickcom/tcpdf/tcpdf.php';
    // require_once WP_CONTENT_DIR . '/plugins/setasign/fpdi/src/autoload.php';
    // $template_name = ['', 'template2.pdf', 'template2_2.pdf', 'template2_3.pdf', 'template2_4.pdf'];
    // $downloadedPdf_name = ['', '_Smlouva_o_dilo_M_Kubát_č. 202289004337.pdf', 'Příloha č.1 - Cenová nabídka_M_Kubát.pdf', 'Příloha č.2_Předsmluvní informace_ke smlouvě_M_Kubát.pdf', 'Příloha č.3 - VZOR odstoupení od smlouvy_M_Kubát.pdf'];

    // for ($j = 1; $j <= 4; $j++) {
    //     $pdf[$j] = new \setasign\Fpdi\Tcpdf\Fpdi('P','mm',array(250,350));
    //     $pagecount = $pdf[$j]->setSourceFile($template_name[$j]);
    
       
    //     for ($i=1; $i < $pagecount + 1; $i++) { 
    //         $tplidx = $pdf[$j]->importPage($i);
    //         $pdf[$j]->AddPage();
    //         $pdf[$j]->useTemplate($tplidx);
    //     }
            
    //     ob_end_clean();
    //     $file_name = $name.$downloadedPdf_name[$j];
    //     $pdf[$j]->Output($file_name, 'D');
    // }
    // exit;

    require_once WP_CONTENT_DIR . '/plugins/tecnickcom/tcpdf/tcpdf.php';
    require_once WP_CONTENT_DIR . '/plugins/setasign/fpdi/src/autoload.php';

    $pdf = new \setasign\Fpdi\Tcpdf\Fpdi('P','mm',array(250,350));
    $pagecount = $pdf->setSourceFile('template2.pdf');

    
    for ($i=1; $i < $pagecount + 1; $i++) { 
        $tplidx = $pdf->importPage($i);
        $pdf->AddPage();
        $pdf->useTemplate($tplidx);
    }
        
    ob_end_clean();
    $file_name = $name . '_Smlouva_o_dilo_M_Kubát_č. 202289004337.pdf';
    $pdf->Output($file_name, 'D');
    exit;
}


add_action('admin_post_download_zakaznic_pdf_action', 'download_zakaznic_pdf');
add_action('admin_post_nopriv_download_zakaznic_pdf_action', 'download_zakaznic_pdf');

function download_zakaznic_pdf() {
    $post_id = $_POST['selected_zakaznic_post_id'];
    $name = get_post_meta($post_id, 'zakaznik', true);
    $address = get_post_meta($post_id, 'adresa_instalace', true);
    $kraj = get_post_meta($post_id, 'kraj', true);
    $telefon = get_post_meta($post_id, 'telefon', true);
    $mail = get_post_meta($post_id, 'e-mail', true);
    $elektromer_stav = get_post_meta($post_id, 'elektromer_stav', true);
    $elektromer_umisteni = get_post_meta($post_id, 'elektromer_umisteni', true);
    $elektromer_vyska_od_zeme = get_post_meta($post_id, 'elektromer_vyska_od_zeme', true);
    $hlavni_domovni_rozvadec_volne_moduly = get_post_meta($post_id, 'hlavni_domovni_rozvadec_volne_moduly', true);
    $akumulace_do = get_post_meta($post_id, 'akumulace_do', true);
    $pocet_osob = get_post_meta($post_id, 'pocet_osob', true);
    $objem_bojleru = get_post_meta($post_id, 'objem_bojleru', true);
    $pripojeni_k_siti = get_post_meta($post_id, 'pripojeni_k_siti', true);
    $elektro_spotreba_v_mwh = get_post_meta($post_id, 'elektro_spotreba_v_mwh', true);
    $cena_za_kw = get_post_meta($post_id, 'cena_za_kw', true);
    $distributor = get_post_meta($post_id, 'distributor', true);
    $cislo_mista_spotreby_ean = get_post_meta($post_id, 'cislo_mista_spotreby_ean', true);
    $typ_strechy = get_post_meta($post_id, 'typ_strechy', true);
    $orientace_strechy = get_post_meta($post_id, 'orientace_strechy', true);
    $rozmer_strechy = get_post_meta($post_id, 'rozmer_strechy', true);
    $material_krytiny = get_post_meta($post_id, 'material_krytiny', true);
    $hlavni_jistic_faze = get_post_meta($post_id, 'hlavni_jistic_faze', true);
    $hlavni_jistic_proud = get_post_meta($post_id, 'hlavni_jistic_proud', true);
    $hlavni_jistic_char = get_post_meta($post_id, 'hlavni_jistic_char', true);
    $hlavni_jistic_zkrat = get_post_meta($post_id, 'hlavni_jistic_zkrat', true);
    $poznamky = get_post_meta($post_id, 'poznamky', true);



    $datetime = get_post_meta($post_id, 'datum', true);
    $balicek_id = get_post_meta($post_id, 'vyberte_balicek', true);
    list($date, $time) = explode(" ", $datetime);
    $date_pdf = str_replace('-', '.', $date);
    $date = str_replace('-', '', $date);
    $smlouva_number = $date.$post_id;

    require_once WP_CONTENT_DIR . '/plugins/tecnickcom/tcpdf/tcpdf.php';
    require_once WP_CONTENT_DIR . '/plugins/setasign/fpdi/src/autoload.php';
    $pdf = new \setasign\Fpdi\Tcpdf\Fpdi('P','mm',array(250,350));
    
    $pagecount = $pdf->setSourceFile('templete3.pdf');


    
    
    $tplidx = $pdf->importPage(1);
    $pdf->AddPage();
    $pdf->useTemplate($tplidx);

    $pdf->SetTextColor(0, 0 , 0);
    $pdf->SetFont('exo2b', '', 11);
    $pdf->writeHTMLCell(0, 2, 207, 18, "{$smlouva_number}", 0, 0, 0, false, '', false);
    $date = date_create($smlouva_number);
    
    $pdf->writeHTMLCell(0, 2, 150, 46, "{$name}", 0, 0, 0, false, '', false);
    
    $pdf->SetFont('exo2light', '', 11);
    $pdf->writeHTMLCell(0, 2, 167, 51, "{$address}", 0, 0, 0, false, '', false);

    $pdf->writeHTMLCell(0, 2, 161, 56.7, "{$kraj}", 0, 0, 0, false, '', false);

    $pdf->writeHTMLCell(0, 2, 159, 62.6, "{$telefon}", 0, 0, 0, false, '', false);

    $pdf->writeHTMLCell(0, 2, 165, 68, "{$mail}", 0, 0, 0, false, '', false);

    $pdf->writeHTMLCell(0, 2, 24, 93.5, "{$elektromer_stav}", 0, 0, 0, false, '', false);

    $pdf->writeHTMLCell(0, 2, 33, 99.5, "{$elektromer_umisteni}", 0, 0, 0, false, '', false);

    $pdf->writeHTMLCell(0, 2, 46, 105, "{$elektromer_vyska_od_zeme}", 0, 0, 0, false, '', false);

    $pdf->writeHTMLCell(0, 2, 44, 121, "{$hlavni_domovni_rozvadec_volne_moduly}", 0, 0, 0, false, '', false);

    $pdf->writeHTMLCell(0, 2, 123, 93.5, "{$akumulace_do}", 0, 0, 0, false, '', false);

    $pdf->writeHTMLCell(0, 2, 118, 99.5, "{$pocet_osob}", 0, 0, 0, false, '', false);

    $pdf->writeHTMLCell(0, 2, 125, 104.8, "{$objem_bojleru}", 0, 0, 0, false, '', false);

    $pdf->writeHTMLCell(0, 2, 125, 110.8, "{$pripojeni_k_siti}", 0, 0, 0, false, '', false);

    $pdf->writeHTMLCell(0, 2, 114, 126.6, "{$elektro_spotreba_v_mwh}", 0, 0, 0, false, '', false);

    $pdf->writeHTMLCell(0, 2, 118, 132, "{$cena_za_kw} Kč ", 0, 0, 0, false, '', false);

    $pdf->writeHTMLCell(0, 2, 116, 137.7, "{$distributor}", 0, 0, 0, false, '', false);

    $pdf->writeHTMLCell(0, 2, 104, 143.5, "{$cislo_mista_spotreby_ean}", 0, 0, 0, false, '', false);

    $pdf->writeHTMLCell(0, 2, 197, 93.5, "{$typ_strechy}", 0, 0, 0, false, '', false);

    $pdf->writeHTMLCell(0, 2, 201, 99.5, "{$orientace_strechy}", 0, 0, 0, false, '', false);

    $pdf->writeHTMLCell(0, 2, 190, 105, "{$rozmer_strechy} M", 0, 0, 0, false, '', false);
    
    $pdf->writeHTMLCell(0, 2, 188, 110.5, "{$material_krytiny} M", 0, 0, 0, false, '', false);

    $pdf->writeHTMLCell(0, 2, 183, 126.5, "{$hlavni_jistic_faze}", 0, 0, 0, false, '', false);

    $pdf->writeHTMLCell(0, 2, 185, 132.5, "{$hlavni_jistic_proud}", 0, 0, 0, false, '', false);

    $pdf->writeHTMLCell(0, 2, 182, 137.8, "{$hlavni_jistic_char}", 0, 0, 0, false, '', false);

    $pdf->writeHTMLCell(0, 2, 183, 143.5, "{$hlavni_jistic_zkrat}", 0, 0, 0, false, '', false);

    $pdf->writeHTMLCell(0, 2, 12, 176, "{$poznamky}", 0, 0, 0, false, '', false);
    
    
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
    if (empty($datetime)) $datetime = get_the_date( 'Y-m-d H:i:s', $post_id);
    $balicek_id = get_post_meta($post_id, 'vyberte_balicek', true);
    list($date, $time) = explode(" ", $datetime);
    $date_pdf = str_replace('-', '.', $date);
    $date = str_replace('-', '', $date);
    $smlouva_number = $date.$post_id;

    $panel_n = get_post_meta( $balicek_id, 'defaultni_pocet_panelu', true);
    $baterie_n = get_post_meta( $balicek_id, 'defaultni_pocet_baterii', true);
    $stridac_n = get_post_meta( $balicek_id, 'defaultni_pocet_stridacu', true);

    $panel_id = get_post_meta($balicek_id, 'panel', true);
    $baterie_id = get_post_meta($balicek_id, 'baterie', true);
    $stridac_id = get_post_meta($balicek_id, 'stridac', true);
    
    $panel = get_field('panel', $balicek_id);
    $panel_name = $panel->post_title;
    $panel_vyrobce =  get_field('vyrobce', $panel_id);
    $panel_vyrobce_name = $panel_vyrobce->post_title;
    $panel_pocet = get_post_meta($balicek_id, 'defaultni_pocet_panelu', true);
    $panel_popis = get_post_meta($panel_id, 'popis', true);
    $panel_svt = get_post_meta($panel_id, 'svt', true);
    $panel_cena_nakup = get_post_meta($panel_id, 'cena_nakup', true);
    $panel_cena_celkem = (int)$panel_cena_nakup * (int)$panel_n;

    $baterie = get_field('baterie', $balicek_id);
    $baterie_name = $baterie->post_title;
    $baterie_vyrobce =  get_field('vyrobce', $baterie_id);
    $baterie_vyrobce_name = $baterie_vyrobce->post_title;
    $baterie_pocet = get_post_meta($balicek_id, 'defaultni_pocet_baterii', true);
    $baterie_popis = get_post_meta($baterie_id, 'popis', true);
    $baterie_cena_nakup = get_post_meta($panel_id, 'cena_nakup', true);
    $baterie_cena_celkem =  (int)$baterie_cena_nakup * (int)$baterie_n;
    
    $stridac = get_field('stridac', $balicek_id);
    $stridac_name = $stridac->post_title;
    $stridac_vyrobce =  get_field('vyrobce', $stridac_id);
    $stridac_vyrobce_name = $stridac_vyrobce->post_title;
    $stridac_pocet = get_post_meta($balicek_id, 'defaultni_pocet_stridacu', true);
    $stridac_popis = get_post_meta($stridac_id, 'popis', true);
    $stridac_svt = get_post_meta($stridac_id, 'svt', true);
    $stridac_cena_nakup = get_post_meta($stridac_id, 'cena_nakup', true);
    $stridac_cena_celkem = (int)$stridac_cena_nakup * (int)$stridac_n;

    $real_price = (int)get_post_meta($post_id, 'vlastni_investice_celkem', true);
    $cena_bez_dph = $real_price / 0.85;
    $cena_bez_dph = (int)$cena_bez_dph;
    $dph = (int)$cena_bez_dph * 0.15;
    $dph = (int)$dph;
    $cena_celkem = $cena_bez_dph - $dph;

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


    $pdf->SetFont('exo2light', '', 12);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->writeHTMLCell(0, 2, 12, 75, "{$panel_vyrobce_name} - {$panel_name}", 0, 0, 0, false, '', false);
    $pdf->writeHTMLCell(0, 2, 120, 75, "{$panel_pocet}", 0, 0, 0, false, '', false);
    $pdf->writeHTMLCell(0, 2, 137, 75, "{$panel_svt}", 0, 0, 0, false, '', false);
    $pdf->writeHTMLCell(0, 2, 175, 75, "{$panel_cena_nakup} Kč", 0, 0, 0, false, '', false);
    $pdf->writeHTMLCell(0, 2, 210, 75, "{$panel_cena_celkem} Kč", 0, 0, 0, false, '', false);

    $pdf->writeHTMLCell(0, 2, 12, 84, "{$stridac_vyrobce_name} - {$stridac_name}", 0, 0, 0, false, '', false);
    $pdf->writeHTMLCell(0, 2, 120, 84, "{$stridac_pocet}", 0, 0, 0, true, '', false);
    $pdf->writeHTMLCell(0, 2, 137, 84, "{$stridac_svt}", 0, 0, 0, true, '', false);
    $pdf->writeHTMLCell(0, 2, 175, 84, "{$stridac_cena_nakup} Kč", 0, 0, 0, false, '', false);
    $pdf->writeHTMLCell(0, 2, 210, 84, "{$stridac_cena_celkem} Kč", 0, 0, 0, false, '', false);


    $pdf->writeHTMLCell(0, 2, 12, 93, "{$baterie_vyrobce_name} - {$baterie_name}", 0, 0, 0, false, '', false);
    $pdf->writeHTMLCell(0, 2, 120, 93, "{$baterie_pocet}", 0, 0, 0, false, '', false);
    $pdf->writeHTMLCell(0, 2, 147, 93, "-", 0, 0, 0, true, '', false);
    $pdf->writeHTMLCell(0, 2, 175, 93, "{$baterie_cena_nakup} Kč", 0, 0, 0, false, '', false);
    $pdf->writeHTMLCell(0, 2, 210, 93, "{$baterie_cena_celkem} Kč", 0, 0, 0, false, '', false);


    $i=0;
    foreach ($balicek_komponenty as $key => $value) {
        $y = $i* 9 + 102;
        $cena_nakup = get_post_meta($value->ID, 'cena_prodej', true);
        $cena_prodej = get_post_meta($value->ID, 'cena_prodej', true);

        $pdf->writeHTMLCell(0, 2, 12, $y, "{$value->post_title}", 0, 0, 0, false, '', false);
        $pdf->writeHTMLCell(0, 2, 120, $y, "1", 0, 0, 0, false, '', false);
        $pdf->writeHTMLCell(0, 2, 147, $y, "-", 0, 0, 0, true, '', false);
       
        $pdf->writeHTMLCell(0, 2, 175, $y, "{$cena_nakup} Kč", 0, 0, 0, true, '', false);
        $pdf->writeHTMLCell(0, 2, 210, $y, "{$cena_prodej} Kč", 0, 0, 0, true, '', false);
        $i++;
    }

    $cena_konstrukce = get_post_meta($post_id, 'cena_konstrukce', true);
    if($cena_konstrukce !== '0' || $cena_konstrukce === ''){
        $pdf->writeHTMLCell(0, 2, 12, $y + 9, "Střecha - konstrukce", 0, 0, 0, false, '', false);
        $pdf->writeHTMLCell(0, 2, 120, $y + 9, "1", 0, 0, 0, false, '', false);
        $pdf->writeHTMLCell(0, 2, 147, $y + 9, "-", 0, 0, 0, true, '', false);
        $pdf->writeHTMLCell(0, 2, 175, $y + 9, "{$cena_konstrukce} Kč", 0, 0, 0, true, '', false);
        $pdf->writeHTMLCell(0, 2, 210, $y + 9, "{$cena_konstrukce} Kč", 0, 0, 0, true, '', false);
    }

    $vice_prace = get_post_meta($post_id, 'vice_prace', true);
    if($vice_prace !== '0' || $vice_prace === ''){
        $pdf->writeHTMLCell(0, 2, 12, $y + 18, "Více práce", 0, 0, 0, false, '', false);
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