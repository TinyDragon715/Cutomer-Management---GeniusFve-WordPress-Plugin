<?php
function add_pdfs_to_zakaznici($pdf, $file_name, $post_id, $client_name, $flag) {
    $uploaddir = wp_upload_dir();
    $uploadfile = $uploaddir['path'] . '/' . $file_name;
    $pdf->Output($uploadfile, 'F');

    $wp_filetype = wp_check_filetype(basename($file_name), null);

    $attachment = array(
        'post_mime_type' => $wp_filetype['type'],
        'post_title' => $client_name,
        'post_content' => '',
        'post_status' => 'inherit'
    );

    $attach_id = wp_insert_attachment( $attachment, $uploadfile );

    $imagenew = get_post( $attach_id );
    $fullsizepath = get_attached_file( $imagenew->ID );
    $attach_data = wp_generate_attachment_metadata( $attach_id, $fullsizepath );
    wp_update_attachment_metadata( $attach_id, $attach_data );

    $zakaznici_post = get_page_by_title($client_name, OBJECT, 'zakaznik');
    $zakaznici_post_id = $zakaznici_post->ID;
    if ($flag == 1) {
        update_post_meta($zakaznici_post_id, 'nabidky_pdf_nabidku', $attach_id);
    } else if ($flag == 2) {
        update_post_meta($zakaznici_post_id, 'nabidky_smlouvu', $attach_id);
    } else if ($flag == 3) {
        update_post_meta($zakaznici_post_id, 'nabidky_obhlidkovy_formular', $attach_id);
    } else if ($flag == 4) {
        update_post_meta($zakaznici_post_id, 'nabidky_rozpoctovou_tabulku', $attach_id);
    }

    update_post_meta($zakaznici_post_id, 'nabidky_created_date', get_the_date("Y-m-d", $post_id));
}

// download pdf
add_action('admin_post_download_pdf_action', 'prev_download_pdf');
add_action('admin_post_nopriv_download_pdf_action', 'prev_download_pdf');
function prev_download_pdf() {
    download_pdf($_POST['selected_post_id'], 0);
}

function download_pdf($nabidky_id, $download_flag) {
    $offer_id       = get_post_meta($nabidky_id, 'c', true);
    $client_name    = get_post_meta($nabidky_id, 'zakaznik', true);
    $address        = get_post_meta($nabidky_id, 'adresa_instalace', true);
    $valid_date     = get_post_meta($nabidky_id, 'datum', true);
    $valid_date     = get_the_date('Y-m-d', $nabidky_id);
    $valid_date     = date('d.m.Y', strtotime("-1 day", strtotime("+1 month", strtotime($valid_date))));

    $balicek_id = get_post_meta($nabidky_id, 'vyberte_balicek', true);
    $dotaci_id = get_post_meta($nabidky_id, 'vyberte_dotaci', true);
    $dotace_title = get_post_meta($dotaci_id, 'nazev', true);
    $cena_konstrukce = get_post_meta($nabidky_id, 'cena_konstrukce', true);
    
    $dotace_price = get_post_meta($nabidky_id, 'dotace_vyse', true);
    if($dotace_price === ''){
        $dotace_price = 0;
    }
    $real_price = (int)get_post_meta($nabidky_id, 'vlastni_investice_celkem', true);
    $cena_celkem = $real_price + (int)$dotace_price;
    $cena_bez_dph = floatval($cena_celkem) / 1.15;
    $cena_bez_dph = round($cena_bez_dph, 0);
    $cena_bez_dph = (int)$cena_bez_dph;
    $dph = $cena_celkem - $cena_bez_dph;
    $final_price = $real_price;

    $panel_id = get_post_meta($balicek_id, 'panel', true);
    $panel = get_field('panel', $balicek_id);
    $panel_name = $panel->post_title;
    $panel_vyrobce =  get_field('vyrobce', $panel_id);
    $panel_vyrobce_name = $panel_vyrobce->post_title;
    $panel_pocet = get_post_meta($nabidky_id, 'pocet_panelu', true);
    $panel_vykon = get_post_meta($panel_id, 'vykon', true);
    $panel_popis = get_post_meta($panel_id, 'popis', true);
    $panel_o_vykonu = $panel_vykon * $panel_pocet;

    $baterie_id = get_post_meta($balicek_id, 'baterie', true);
    $baterie = get_field('baterie', $balicek_id);
    $baterie_name = $baterie->post_title;
    $baterie_vyrobce =  get_field('vyrobce', $baterie_id);
    $baterie_vyrobce_name = $baterie_vyrobce->post_title;
    $baterie_pocet = get_post_meta($nabidky_id, 'pocet_baterii', true);
    $baterie_kapacita = get_post_meta($baterie_id, 'kapacita', true);
    $baterie_popis = get_post_meta($baterie_id, 'popis', true);
    $baterie_kapacitou = $baterie_kapacita * $baterie_pocet;
    
    $stridac_id = get_post_meta($balicek_id, 'stridac', true);
    $stridac = get_field('stridac', $balicek_id);
    $stridac_name = $stridac->post_title;
    $stridac_vyrobce =  get_field('vyrobce', $stridac_id);
    $stridac_vyrobce_name = $stridac_vyrobce->post_title;
    $stridac_pocet = get_post_meta($nabidky_id, 'pocet_stridacu', true);
    $stridac_popis = get_post_meta($stridac_id, 'popis', true);

    $balicek_komponenty = get_field('komponenty', $balicek_id);
    
    require_once WP_CONTENT_DIR . '/plugins/tecnickcom/tcpdf/tcpdf.php';
    require_once WP_CONTENT_DIR . '/plugins/setasign/fpdi/src/autoload.php';
    $pdf = new \setasign\Fpdi\Tcpdf\Fpdi('P','mm',array(250,350));
    $pdf->setSourceFile('templete1.pdf');

    // Page 1.
    $tplidx = $pdf->importPage(1);
    $pdf->AddPage();
    $pdf->useTemplate($tplidx);
    // KLIENT
    $pdf->SetTextColor(17, 115, 160);
    $pdf->SetFont('exo2b', '', 12);
    $pdf->writeHTMLCell(0, 2, 9, 92, "{$client_name}", 0, 0, 0, false, '', false);
    // ČÍSLO NABÍDKY
    $pdf->SetFont('exo2b', '', 14);
    $pdf->writeHTMLCell(0, 2, 140, 18, "{$offer_id}", 0, 0, 0, false, '', false);
    // ADRESA
    $pdf->writeHTMLCell(0, 2, 9, 115, "{$address}", 0, 0, 0, false, '', false);
    // PLATNOST DO
    $pdf->writeHTMLCell(0, 2, 9, 137, "{$valid_date}", 0, 0, 0, false, '', false);

    // Page 2.
    $tplidx = $pdf->importPage(2);
    $pdf->AddPage();
    $pdf->useTemplate($tplidx);

    $pdf->SetFont('exo2b', '', 17);
    $pdf->SetTextColor(17, 115, 160);
    // O VÝKONU WP
    $pdf->writeHTMLCell(0, 2, 182, 13, "O VÝKONU {$panel_o_vykonu}WP", 0, 0, 0, false, '', false);
    // KAPACITOU BATERIE KWH
    $pdf->writeHTMLCell(0, 2, 26, 24.5, "KAPACITOU BATERIE {$baterie_kapacitou}KWH", 0, 0, 0, false, '', false);

    $pdf->SetFont('exo2light', '', 12);
    $pdf->SetTextColor(0, 0, 0);
    // Panel
    $pdf->writeHTMLCell(0, 2, 15, 70, "{$panel_id}", 0, 0, 0, false, '', false);
    $pdf->writeHTMLCell(0, 2, 34.5, 70, "{$panel_vyrobce_name} - {$panel_name}", 0, 0, 0, false, '', false);
    $pdf->writeHTMLCell(0, 2, 220, 70, "{$panel_pocet}", 0, 0, 0, false, '', false);
    // Baterie
    $pdf->writeHTMLCell(0, 2, 15, 82, "{$baterie_id}", 0, 0, 0, false, '', false);
    $pdf->writeHTMLCell(0, 2, 34.5, 82, "{$baterie_vyrobce_name} - {$baterie_name}", 0, 0, 0, false, '', false);
    $pdf->writeHTMLCell(0, 2, 220, 82, "{$baterie_pocet}", 0, 0, 0, true, '', false);
    // Stridac
    $pdf->writeHTMLCell(0, 2, 15, 94, "{$stridac_id}", 0, 0, 0, false, '', false);
    $pdf->writeHTMLCell(0, 2, 34.5, 94, "{$stridac_vyrobce_name} - {$stridac_name}", 0, 0, 0, false, '', false);
    $pdf->writeHTMLCell(0, 2, 220, 94, "{$stridac_pocet}", 0, 0, 0, false, '', false);
    // Komponenty
    $i = 0;
    foreach ($balicek_komponenty as $key => $value) {
        $y = ($i++) * 12 + 106;
        // Komponenty-id
        $pdf->SetFont('exo2b', '', 12);
        $pdf->writeHTMLCell(0, 2, 15, $y, "{$value->ID}", 0, 0, 0, false, '', false);
        // Komponenty-name
        $pdf->SetFont('exo2light', '', 12);
        $pdf->writeHTMLCell(0, 2, 34.5, $y, "{$value->post_title}", 0, 0, 0, false, '', false);
        // Komponenty-pocet
        $pdf->writeHTMLCell(0, 2, 220, $y, "1", 0, 0, 0, false, '', false);
    }
    // Cena Konstrukce
    if($cena_konstrukce !== '0' || $cena_konstrukce === ''){
        $pdf->writeHTMLCell(0, 2, 18, $y + 12, "22", 0, 0, 0, false, '', false);
        $pdf->writeHTMLCell(0, 2, 34.5, $y + 12, "Střecha - konstrukce", 0, 0, 0, false, '', false);   
        $pdf->writeHTMLCell(0, 2, 220, $y + 12, "1", 0, 0, 0, false, '', false);
    }
    // Dotace
    $pdf->writeHTMLCell(0, 2, 19, $y + 24, "#", 0, 0, 0, false, '', false);
    $pdf->writeHTMLCell(0, 2, 34.5, $y + 24, "Dotace celkem: {$dotace_title}", 0, 0, 0, false, '', false);
    $pdf->writeHTMLCell(0, 2, 220, $y + 24, "1", 0, 0, 0, false, '', false);

    $cena_bez_dph = number_format($cena_bez_dph, 0, ".", " ");
    $dph          = number_format($dph, 0, ".", " ");
    $cena_celkem  = number_format($cena_celkem,  0, ".", " ");
    $dotace_price = number_format($dotace_price, 0, ".", " ");
    $final_price  = number_format($final_price, 0, ".", " ");
    // Result
    $pdf->SetFont('exo2b', '', 12);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->writeHTMLCell(210, 0, 205, 258.7, "{$cena_bez_dph} Kč", 0, 0, 0, true, '', false);
    $pdf->writeHTMLCell(210, 0, 205, 268.5, "{$dph} Kč", 0, 0, 0, true, '', false);
    $pdf->writeHTMLCell(210, 0, 205, 278.5, "{$cena_celkem} Kč", 0, 0, 0, true, '', false);
    $pdf->writeHTMLCell(210, 0, 205, 288.5, "-{$dotace_price} Kč", 0, 0, 0, true, '', false);
    $pdf->SetFont('exo2b', '', 13);
    $pdf->writeHTMLCell(210, 0, 203, 302, "{$final_price} Kč", 0, 0, 0, true, '', false);
    
    // Page 3.
    $tplidx = $pdf->importPage(3);
    $pdf->AddPage();
    $pdf->useTemplate($tplidx);
    
    $pdf->SetTextColor(17, 115,160);
    // Panel
    $pdf->SetFont('exo2b', '', 14);
    $pdf->writeHTMLCell(210, 0, 20, 48, "Solární panely: {$panel_vyrobce_name} - {$panel_name}", 0, 0, 0, true, '', false);
    $pdf->SetFont('exo2b', '', 12);
    $pdf->writeHTMLCell(210, 0, 20, 58, "{$panel_popis}", 0, 0, 0, true, '', false);
    // Inverter
    $pdf->SetFont('exo2b', '', 14); 
    $pdf->writeHTMLCell(210, 0, 20, 100, "Střídač: {$stridac_vyrobce_name} - {$stridac_name}", 0, 0, 0, true, '', false);
    $pdf->SetFont('exo2b', '', 12);
    $pdf->writeHTMLCell(210, 0, 20, 110, "{$stridac_popis}", 0, 0, 0, true, '', false);
    // Battery
    $pdf->SetFont('exo2b', '', 14);
    $pdf->writeHTMLCell(210, 0, 20, 152, "Baterie: {$baterie_vyrobce_name} - {$baterie_name}", 0, 0, 0, true, '', false);
    $pdf->SetFont('exo2b', '', 12);
    $pdf->writeHTMLCell(210, 0, 20, 162, "{$baterie_popis}", 0, 0, 0, true, '', false);

    // Page 4.
    $tplidx = $pdf->importPage(4);
    $pdf->AddPage();
    $pdf->useTemplate($tplidx);

    // Page 5.
    $tplidx = $pdf->importPage(5);
    $pdf->AddPage();
    $pdf->useTemplate($tplidx);
    
    ob_end_clean();
    $file_name = $client_name . '.pdf';
    
    if ($download_flag == 0) {
        $pdf->Output($file_name, 'D');
        exit;
    } else if ($download_flag == 1) {
        add_pdfs_to_zakaznici($pdf, $file_name, $nabidky_id, $client_name, 1);
    }
}

function show_contract_customer_number_for_each_page($pdf, $customer_number) {
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('exo2b', '', 8);
    $pdf->writeHTMLCell(30, 20, 138, 15, "SMLOUVA O DÍLO Č.", 0, 0, 0, true, '', false);

    $pdf->SetTextColor(17, 115,160);
    $pdf->SetFont('exo2b', '', 8);
    $pdf->writeHTMLCell(30, 20, 165, 15, "{$customer_number}", 0, 0, 0, true, '', false);
}

add_action('admin_post_download_contrac_pdf_action', 'prev_download_contrac_pdf');
add_action('admin_post_nopriv_download_contrac_pdf_action', 'prev_download_contrac_pdf');
function prev_download_contrac_pdf() {
    download_contrac_pdf($_POST['selected_contrac_post_id'], 0);
}

function download_contrac_pdf($post_id, $download_flag) {
    $name = get_post_meta($post_id, 'zakaznik', true);
    $email = get_post_meta($post_id, 'e-mail', true);
    $telefon = get_post_meta($post_id, 'telefon', true);
    $address_instalace = get_post_meta($post_id, 'adresa_instalace', true);
    $customer_number = get_post_meta($post_id, 'c', true);

    $zakaznici_post = get_page_by_title($name, OBJECT, 'zakaznik');
    $zakaznici_post_id = $zakaznici_post->ID;
    $trvaly_pobyt = get_post_meta($zakaznici_post_id, 'trvaly_pobyt', true);

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
    $pdf->writeHTMLCell(100, 20, 37, 226, $trvaly_pobyt, 0, 0, 0, true, '', false);

    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('exo2b', '', 10);
    $pdf->writeHTMLCell(100, 20, 37, 236, $email, 0, 0, 0, true, '', false);

    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('exo2b', '', 10);
    $pdf->writeHTMLCell(100, 20, 32, 241, $telefon, 0, 0, 0, true, '', false);

    $tplidx = $pdf->importPage(2);
    $pdf->AddPage();
    $pdf->useTemplate($tplidx);
    show_contract_customer_number_for_each_page($pdf, $customer_number);

    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('exo2b', '', 10);
    $pdf->writeHTMLCell(100, 20, 70, 140.2, $address_instalace, 0, 0, 0, true, '', false);

    for ($i = 3; $i <= 8; $i++) {
        $tplidx = $pdf->importPage($i);
        $pdf->AddPage();
        $pdf->useTemplate($tplidx);
        show_contract_customer_number_for_each_page($pdf, $customer_number);
    }

    $balicek_id     = get_post_meta($post_id, 'vyberte_balicek', true);

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

    $dotace_price = get_post_meta($post_id, 'dotace_vyse', true);
    if($dotace_price === ''){
        $dotace_price = 0;
    }
    $real_price = (int)get_post_meta($post_id, 'vlastni_investice_celkem', true);
    $cena_celkem = $real_price + (int)$dotace_price;
    $cena_bez_dph = floatval($cena_celkem) / 1.15;
    $cena_bez_dph = round($cena_bez_dph, 0);
    $cena_bez_dph = (int)$cena_bez_dph;
    $dph = $cena_celkem - $cena_bez_dph;

    $balicek_komponenty = get_field('komponenty', $balicek_id);

    // Page 9.
    $tplidx = $pdf->importPage(9);
    $pdf->AddPage();
    $pdf->useTemplate($tplidx);

    $pdf->SetTextColor(0, 0 , 0);
    $pdf->SetFont('exo2b', '', 14);
    $pdf->writeHTMLCell(0, 2, 200, 18, "{$smlouva_number}", 0, 0, 0, false, '', false);

    $panel_cena_nakup  = number_format($panel_cena_nakup, 0, ".", " ");
    $panel_cena_celkem = number_format($panel_cena_celkem, 0, ".", " ");
    $pdf->SetFont('exo2light', '', 10);
    $pdf->writeHTMLCell(0, 2, 24, 68, "{$panel_vyrobce_name} - {$panel_name}", 0, 0, 0, false, '', false);
    $pdf->SetFont('exo2light', '', 12);
    $pdf->writeHTMLCell(0, 2, 125, 68, "{$panel_pocet} ks", 0, 0, 0, false, '', false);
    $pdf->writeHTMLCell(0, 2, 165, 68, "{$panel_cena_nakup} Kč", 0, 0, 0, false, '', false);

    $stridac_cena_nakup  = number_format($stridac_cena_nakup, 0, ".", " ");
    $stridac_cena_celkem = number_format($stridac_cena_celkem, 0, ".", " ");
    $pdf->SetFont('exo2light', '', 10);
    $pdf->writeHTMLCell(0, 2, 24, 77, "{$stridac_vyrobce_name} - {$stridac_name}", 0, 0, 0, false, '', false);
    $pdf->SetFont('exo2light', '', 12);
    $pdf->writeHTMLCell(0, 2, 125, 84, "{$stridac_pocet} ks", 0, 0, 0, true, '', false);
    $pdf->writeHTMLCell(0, 2, 165, 84, "{$stridac_cena_nakup} Kč", 0, 0, 0, false, '', false);

    $baterie_cena_nakup  = number_format($baterie_cena_nakup, 0, ".", " ");
    $baterie_cena_celkem = number_format($baterie_cena_celkem, 0, ".", " ");
    $pdf->SetFont('exo2light', '', 10);
    $pdf->writeHTMLCell(0, 2, 24, 93, "{$baterie_vyrobce_name} - {$baterie_name}", 0, 0, 0, false, '', false);
    $pdf->SetFont('exo2light', '', 12);
    $pdf->writeHTMLCell(0, 2, 125, 93, "{$baterie_pocet} ks", 0, 0, 0, false, '', false);
    $pdf->writeHTMLCell(0, 2, 165, 93, "{$baterie_cena_nakup} Kč", 0, 0, 0, false, '', false);

    $i = 0;
    foreach ($balicek_komponenty as $key => $value) {
        $y = ($i++) * 9 + 102;
        $cena_nakup = get_post_meta($value->ID, 'cena_prodej', true);

        $pdf->SetFont('exo2light', '', 10);
        $pdf->writeHTMLCell(0, 2, 24, $y, "{$value->post_title}", 0, 0, 0, false, '', false);
        $pdf->SetFont('exo2light', '', 12);
        $pdf->writeHTMLCell(0, 2, 125, $y, "1 ks", 0, 0, 0, false, '', false);
       
        $cena_nakup = number_format($cena_nakup, 0, ".", " ");
        $pdf->writeHTMLCell(0, 2, 165, $y, "{$cena_nakup} Kč", 0, 0, 0, true, '', false);
    }

    $cena_konstrukce = get_post_meta($post_id, 'cena_konstrukce', true);
    if($cena_konstrukce !== '0' || $cena_konstrukce === ''){
        if ($cena_konstrukce == "")
            $cena_konstrukce = 0;
        
        $cena_konstrukce = number_format($cena_konstrukce, 0, ".", " ");
        $pdf->SetFont('exo2light', '', 10);
        $pdf->writeHTMLCell(0, 2, 24, $y + 9, "Střecha - konstrukce", 0, 0, 0, false, '', false);
        $pdf->SetFont('exo2light', '', 12);
        $pdf->writeHTMLCell(0, 2, 125, $y + 9, "1 ks", 0, 0, 0, false, '', false);
        $pdf->writeHTMLCell(0, 2, 165, $y + 9, "{$cena_konstrukce} Kč", 0, 0, 0, true, '', false);
    }

    $vice_prace = get_post_meta($post_id, 'vice_prace', true);
    if($vice_prace !== '0' || $vice_prace === ''){
        if ($vice_prace == "")
            $vice_prace = 0;

        $vice_prace = number_format($vice_prace, 0, ".", " ");
        $pdf->SetFont('exo2light', '', 10);
        $pdf->writeHTMLCell(0, 2, 24, $y + 18, "Více práce", 0, 0, 0, false, '', false);
        $pdf->SetFont('exo2light', '', 12);
        $pdf->writeHTMLCell(0, 2, 125, $y + 18, "1 ks", 0, 0, 0, false, '', false);
        $pdf->writeHTMLCell(0, 2, 165, $y + 18, "{$vice_prace} Kč", 0, 0, 0, true, '', false);
    }

    $cena_bez_dph = number_format($cena_bez_dph, 0, ".", " ");
    $dph          = number_format($dph, 0, ".", " ");
    $cena_celkem  = number_format($cena_celkem, 0, ".", " ");

    $pdf->SetFont('exo2light', '', 10);
    $pdf->writeHTMLCell(0, 2, 24, $y + 27, "Cena bez DPH", 0, 0, 0, false, '', false);
    $pdf->SetFont('exo2light', '', 12);
    $pdf->writeHTMLCell(0, 2, 165, $y + 27, "{$cena_bez_dph} Kč", 0, 0, 0, false, '', false);

    $pdf->SetFont('exo2light', '', 10);
    $pdf->writeHTMLCell(0, 2, 24, $y + 36, "DPH", 0, 0, 0, false, '', false);
    $pdf->SetFont('exo2light', '', 12);
    $pdf->writeHTMLCell(0, 2, 165, $y + 36, "{$dph} Kč", 0, 0, 0, false, '', false);

    $pdf->SetFont('exo2light', '', 10);
    $pdf->writeHTMLCell(0, 2, 24, $y + 45, "Cena celkem", 0, 0, 0, false, '', false);
    $pdf->SetFont('exo2light', '', 12);
    $pdf->writeHTMLCell(0, 2, 165, $y + 45, "{$cena_celkem} Kč", 0, 0, 0, false, '', false);

    $panel_id = get_post_meta($balicek_id, 'panel', true);
    $panel = get_field('panel', $balicek_id);
    $panel_name = $panel->post_title;
    $panel_vyrobce =  get_field('vyrobce', $panel_id);
    $panel_vyrobce_name = $panel_vyrobce->post_title;
    $panel_pocet = get_post_meta($post_id, 'pocet_panelu', true);
    $panel_vykon = get_post_meta($panel_id, 'vykon', true);
    $panel_popis = get_post_meta($panel_id, 'popis', true);
    $panel_o_vykonu = $panel_vykon * $panel_pocet;

    $baterie_id = get_post_meta($balicek_id, 'baterie', true);
    $baterie = get_field('baterie', $balicek_id);
    $baterie_name = $baterie->post_title;
    $baterie_vyrobce =  get_field('vyrobce', $baterie_id);
    $baterie_vyrobce_name = $baterie_vyrobce->post_title;
    $baterie_pocet = get_post_meta($post_id, 'pocet_baterii', true);
    $baterie_kapacita = get_post_meta($baterie_id, 'kapacita', true);
    $baterie_popis = get_post_meta($baterie_id, 'popis', true);
    $baterie_kapacitou = $baterie_kapacita * $baterie_pocet;
    
    $stridac_id = get_post_meta($balicek_id, 'stridac', true);
    $stridac = get_field('stridac', $balicek_id);
    $stridac_name = $stridac->post_title;
    $stridac_vyrobce =  get_field('vyrobce', $stridac_id);
    $stridac_vyrobce_name = $stridac_vyrobce->post_title;
    $stridac_pocet = get_post_meta($post_id, 'pocet_stridacu', true);
    $stridac_popis = get_post_meta($stridac_id, 'popis', true);

    // Page 10.
    $tplidx = $pdf->importPage(10);
    $pdf->AddPage();
    $pdf->useTemplate($tplidx);

    $tbl = '
    <table style="border: 1px solid #CFDBEF" cellspacing="0" cellpadding="10">
        <tr style="border: 1px solid #CFDBEF">
            <td style="border: 1px solid #CFDBEF">Solární panely: ' . $panel_vyrobce_name . ' - ' . $panel_name . '</td>
            <td style="border: 1px solid #CFDBEF">' . $panel_popis . '</td>
        </tr>
        <tr style="border: 1px solid #CFDBEF; background: #CFDBEF;">
            <td style="border: 1px solid #CFDBEF; background: #CFDBEF;">Střídač: ' . $stridac_vyrobce_name . ' - ' . $stridac_name . '</td>
            <td style="border: 1px solid #CFDBEF; background: #CFDBEF;">' . $stridac_popis . '</td>
        </tr>
        <tr style="border: 1px solid #CFDBEF">
            <td style="border: 1px solid #CFDBEF">Baterie: ' . $baterie_vyrobce_name . ' - ' . $baterie_name . '</td>
            <td style="border: 1px solid #CFDBEF">' . $baterie_popis . '</td>
        </tr>
    </table>
    ';

    $pdf->SetTextColor(47, 84, 150);
    $pdf->SetFont('exo2light', '', 12);
    $pdf->writeHTMLCell(0, 2, 20, 90, $tbl, 0, 0, 0, false, '', false);

    for ($i = 11; $i <= 15; $i++) {
        $tplidx = $pdf->importPage($i);
        $pdf->AddPage();
        $pdf->useTemplate($tplidx);
        show_contract_customer_number_for_each_page($pdf, $customer_number);
    }

    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('exo2b', '', 10);
    $pdf->writeHTMLCell(100, 20, 117, 136, "Č. {$customer_number}", 0, 0, 0, true, '', false);

    ob_end_clean();
    $file_name = $name . '_Smlouva_o_dilo_č. ' . $customer_number . '.pdf';

    if ($download_flag == 0) {
        $pdf->Output($file_name, 'D');
        exit;
    } else if ($download_flag == 1) {
        add_pdfs_to_zakaznici($pdf, $file_name, $post_id, $name, 2);
    }
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

add_action('admin_post_download_zakaznic_pdf_action', 'prev_download_zakaznic_pdf');
add_action('admin_post_nopriv_download_zakaznic_pdf_action', 'prev_download_zakaznic_pdf');
function prev_download_zakaznic_pdf() {
    download_zakaznic_pdf($_POST['selected_zakaznic_post_id'], 0);
}

function download_zakaznic_pdf($nabidky_post_id, $download_flag) {
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

    $smlouva_number = get_post_meta($nabidky_post_id, 'c', true);

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

    if ($download_flag == 0) {
        $pdf->Output($file_name, 'D');
        exit;
    } else if ($download_flag == 1) {
        add_pdfs_to_zakaznici($pdf, $file_name, $nabidky_post_id, $name, 3);
    }
}

add_action('admin_post_download_technical_pdf_action', 'prev_download_technical_pdf');
add_action('admin_post_nopriv_download_technical_pdf_action', 'prev_download_technical_pdf');
function prev_download_technical_pdf() {
    download_technical_pdf($_POST['selected_technical_post_id'], 0);
}

function download_technical_pdf($post_id, $download_flag) {
    $name           = get_post_meta($post_id, 'zakaznik', true);
    $balicek_id     = get_post_meta($post_id, 'vyberte_balicek', true);
    $smlouva_number = get_post_meta($post_id, 'c', true);

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

    $dotace_price = get_post_meta($post_id, 'dotace_vyse', true);
    if($dotace_price === ''){
        $dotace_price = 0;
    }
    $real_price = (int)get_post_meta($post_id, 'vlastni_investice_celkem', true);
    $cena_celkem = $real_price + (int)$dotace_price;
    $cena_bez_dph = floatval($cena_celkem) / 1.15;
    $cena_bez_dph = round($cena_bez_dph, 0);
    $cena_bez_dph = (int)$cena_bez_dph;
    $dph = $cena_celkem - $cena_bez_dph;

    $balicek_komponenty = get_field('komponenty', $balicek_id);

    require_once WP_CONTENT_DIR . '/plugins/tecnickcom/tcpdf/tcpdf.php';
    require_once WP_CONTENT_DIR . '/plugins/setasign/fpdi/src/autoload.php';
    $pdf = new \setasign\Fpdi\Tcpdf\Fpdi('P','mm',array(250,350));
    $pdf->setSourceFile('templete4.pdf');

    // Page 1.
    $tplidx = $pdf->importPage(1);
    $pdf->AddPage();
    $pdf->useTemplate($tplidx);

    $pdf->SetTextColor(0, 0 , 0);
    $pdf->SetFont('exo2b', '', 14);
    $pdf->writeHTMLCell(0, 2, 200, 18, "{$smlouva_number}", 0, 0, 0, false, '', false);

    $panel_cena_nakup  = number_format($panel_cena_nakup, 0, ".", " ");
    $panel_cena_celkem = number_format($panel_cena_celkem, 0, ".", " ");
    $pdf->SetFont('exo2light', '', 10);
    $pdf->writeHTMLCell(0, 2, 12, 75, "{$panel_vyrobce_name} - {$panel_name}", 0, 0, 0, false, '', false);
    $pdf->SetFont('exo2light', '', 12);
    $pdf->writeHTMLCell(0, 2, 120, 75, "{$panel_pocet}", 0, 0, 0, false, '', false);
    $pdf->writeHTMLCell(0, 2, 137, 75, "{$panel_svt}", 0, 0, 0, false, '', false);
    $pdf->writeHTMLCell(0, 2, 175, 75, "{$panel_cena_nakup} Kč", 0, 0, 0, false, '', false);
    $pdf->writeHTMLCell(0, 2, 210, 75, "{$panel_cena_celkem} Kč", 0, 0, 0, false, '', false);

    $stridac_cena_nakup  = number_format($stridac_cena_nakup, 0, ".", " ");
    $stridac_cena_celkem = number_format($stridac_cena_celkem, 0, ".", " ");
    $pdf->SetFont('exo2light', '', 10);
    $pdf->writeHTMLCell(0, 2, 12, 84, "{$stridac_vyrobce_name} - {$stridac_name}", 0, 0, 0, false, '', false);
    $pdf->SetFont('exo2light', '', 12);
    $pdf->writeHTMLCell(0, 2, 120, 84, "{$stridac_pocet}", 0, 0, 0, true, '', false);
    $pdf->writeHTMLCell(0, 2, 137, 84, "{$stridac_svt}", 0, 0, 0, true, '', false);
    $pdf->writeHTMLCell(0, 2, 175, 84, "{$stridac_cena_nakup} Kč", 0, 0, 0, false, '', false);
    $pdf->writeHTMLCell(0, 2, 210, 84, "{$stridac_cena_celkem} Kč", 0, 0, 0, false, '', false);

    $baterie_cena_nakup  = number_format($baterie_cena_nakup, 0, ".", " ");
    $baterie_cena_celkem = number_format($baterie_cena_celkem, 0, ".", " ");
    $pdf->SetFont('exo2light', '', 10);
    $pdf->writeHTMLCell(0, 2, 12, 93, "{$baterie_vyrobce_name} - {$baterie_name}", 0, 0, 0, false, '', false);
    $pdf->SetFont('exo2light', '', 12);
    $pdf->writeHTMLCell(0, 2, 120, 93, "{$baterie_pocet}", 0, 0, 0, false, '', false);
    $pdf->writeHTMLCell(0, 2, 147, 93, "-", 0, 0, 0, true, '', false);
    $pdf->writeHTMLCell(0, 2, 175, 93, "{$baterie_cena_nakup} Kč", 0, 0, 0, false, '', false);
    $pdf->writeHTMLCell(0, 2, 210, 93, "{$baterie_cena_celkem} Kč", 0, 0, 0, false, '', false);

    $i = 0;
    foreach ($balicek_komponenty as $key => $value) {
        $y = ($i++) * 9 + 102;
        $cena_nakup = get_post_meta($value->ID, 'cena_prodej', true);

        $pdf->SetFont('exo2light', '', 10);
        $pdf->writeHTMLCell(0, 2, 12, $y, "{$value->post_title}", 0, 0, 0, false, '', false);
        $pdf->SetFont('exo2light', '', 12);
        $pdf->writeHTMLCell(0, 2, 120, $y, "1", 0, 0, 0, false, '', false);
        $pdf->writeHTMLCell(0, 2, 147, $y, "-", 0, 0, 0, true, '', false);
       
        $cena_nakup = number_format($cena_nakup, 0, ".", " ");
        $pdf->writeHTMLCell(0, 2, 175, $y, "{$cena_nakup} Kč", 0, 0, 0, true, '', false);
        $pdf->writeHTMLCell(0, 2, 210, $y, "{$cena_nakup} Kč", 0, 0, 0, true, '', false);
    }

    $cena_konstrukce = get_post_meta($post_id, 'cena_konstrukce', true);
    if($cena_konstrukce !== '0' || $cena_konstrukce === ''){
        if ($cena_konstrukce == "")
            $cena_konstrukce = 0;
        
        $cena_konstrukce = number_format($cena_konstrukce, 0, ".", " ");
        $pdf->SetFont('exo2light', '', 10);
        $pdf->writeHTMLCell(0, 2, 12, $y + 9, "Střecha - konstrukce", 0, 0, 0, false, '', false);
        $pdf->SetFont('exo2light', '', 12);
        $pdf->writeHTMLCell(0, 2, 120, $y + 9, "1", 0, 0, 0, false, '', false);
        $pdf->writeHTMLCell(0, 2, 147, $y + 9, "-", 0, 0, 0, true, '', false);
        $pdf->writeHTMLCell(0, 2, 175, $y + 9, "{$cena_konstrukce} Kč", 0, 0, 0, true, '', false);
        $pdf->writeHTMLCell(0, 2, 210, $y + 9, "{$cena_konstrukce} Kč", 0, 0, 0, true, '', false);
    }

    $vice_prace = get_post_meta($post_id, 'vice_prace', true);
    if($vice_prace !== '0' || $vice_prace === ''){
        if ($vice_prace == "")
            $vice_prace = 0;

        $vice_prace = number_format($vice_prace, 0, ".", " ");
        $pdf->SetFont('exo2light', '', 10);
        $pdf->writeHTMLCell(0, 2, 12, $y + 18, "Více práce", 0, 0, 0, false, '', false);
        $pdf->SetFont('exo2light', '', 12);
        $pdf->writeHTMLCell(0, 2, 120, $y + 18, "1", 0, 0, 0, false, '', false);
        $pdf->writeHTMLCell(0, 2, 147, $y + 18, "-", 0, 0, 0, true, '', false);
        $pdf->writeHTMLCell(0, 2, 175, $y + 18, "{$vice_prace} Kč", 0, 0, 0, true, '', false);
        $pdf->writeHTMLCell(0, 2, 210, $y + 18, "{$vice_prace} Kč", 0, 0, 0, true, '', false);
    }

    $cena_bez_dph = number_format($cena_bez_dph, 0, ".", " ");
    $dph          = number_format($dph, 0, ".", " ");
    $cena_celkem  = number_format($cena_celkem, 0, ".", " ");
    $pdf->SetFont('exo2b', '', 12);
    $pdf->writeHTMLCell(210, 0, 205, 217.7, "{$cena_bez_dph} Kč", 0, 0, 0, true, '', false);
    $pdf->writeHTMLCell(210, 0, 205, 225.7, "{$dph} Kč", 0, 0, 0, true, '', false);
    $pdf->writeHTMLCell(210, 0, 205, 234, "{$cena_celkem} Kč", 0, 0, 0, true, '', false);

    ob_end_clean();
    $file_name = $name.'_technical.pdf';
    
    if ($download_flag == 0) {
        $pdf->Output($file_name, 'D');
        exit;
    } else if ($download_flag == 1){
        add_pdfs_to_zakaznici($pdf, $file_name, $post_id, $name, 4);
    }
}
?>