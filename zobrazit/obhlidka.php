<?php

$customer_id = filter_input( INPUT_GET, "customer_id", FILTER_SANITIZE_STRING );
$post_id = filter_input( INPUT_GET, "post_id", FILTER_SANITIZE_STRING );
$new_form_flag = filter_input( INPUT_GET, "new_form_flag", FILTER_SANITIZE_STRING );

if (!isset($post_id) && $new_form_flag == 0) {
    echo "
    <h1>Nebyly předloženy žádné údaje.</h1>
    <a  href='" . add_query_arg( array(
                    'customer_id' => $customer_id,
                    'post_id' => $post_id,
                    'new_form_flag' => 1,
                ), admin_url('admin.php?page=obhlidka') ) . "'>
        <button class='btn btn-primary'>Vytvořit nový formulář Obhlídka</button>
    </a>
    ";
    return;
}

global $wpdb; 
$metaTable = $wpdb->prefix.'frmt_form_entry_meta';

$select = $text = $radio = $textarea = $number = $date = [];
for ($i = 1; $i <= 13; $i++)
    $select[$i] = $wpdb->get_results ( "SELECT meta_value FROM $metaTable WHERE `entry_id` = $post_id AND `meta_key` = 'select-" . $i . "'" )[0]->meta_value;
for ($i = 1; $i <= 18; $i++)
    $text[$i] = $wpdb->get_results ( "SELECT meta_value FROM $metaTable WHERE `entry_id` = $post_id AND `meta_key` = 'text-" . $i . "'" )[0]->meta_value;
$date[1] = $wpdb->get_results ( "SELECT meta_value FROM $metaTable WHERE `entry_id` = $post_id AND `meta_key` = 'date-1'" )[0]->meta_value;
if ($date[1]) $date[1] = date('Y-m-d', strtotime($date[1]));
$radio[1] = $wpdb->get_results ( "SELECT meta_value FROM $metaTable WHERE `entry_id` = $post_id AND `meta_key` = 'radio-1'" )[0]->meta_value;
for ($i = 1; $i <= 3; $i++)
    $textarea[$i] = $wpdb->get_results ( "SELECT meta_value FROM $metaTable WHERE `entry_id` = $post_id AND `meta_key` = 'textarea-" . $i . "'" )[0]->meta_value;
for ($i = 1; $i <= 3; $i++)
    $number[$i] = $wpdb->get_results ( "SELECT meta_value FROM $metaTable WHERE `entry_id` = $post_id AND `meta_key` = 'number-" . $i . "'" )[0]->meta_value;

$entry = Forminator_API::get_entry( 881, $post_id );
$upload = $upload_url = [];
for ($i = 1; $i <= 15; $i++) {
    $upload[$i] = $entry->meta_data['upload-' . $i]['value'];
    if (!empty($upload[$i])) {
        for ($j = 0; $j < count($upload[$i]['file']['file_url']); $j++)
            $upload_url[$i][$j] = $upload[$i]['file']['file_url'][$j];
    }
}
?>

<div class="row">
    <div class="offset-md-2 col-md-6">
        <h1>Obhlídka</h1>

        <div class="form-content">
        <div class="form-group">
            <label for="date_1">Datum obhlídky</label>
            <input type="date" class="form-control" id="date_1" value="<?php echo $date[1]; ?>">
        </div>

        <div class="form-group">
            <label for="text_16">Kontakt na zákazníka</label>
            <input type="text" class="form-control" id="text_16" value="<?php echo $text[16]; ?>" placeholder="Zde vyplňte">
        </div>

        <div class="form-group">
            <label for="text_17">Jméno a příjmení</label>
            <input type="text" class="form-control" id="text_17" value="<?php echo $text[17]; ?>" placeholder="Zde vyplňte">
        </div>

        <div class="form-group">
            <label for="select_1">Typ konstrukce</label>
            <select class="form-control" id="select_1">
                <option value="Kombi vrut" <?php if($select[1] == 'Kombi vrut') echo "selected"; ?>>Kombi vrut</option>
                <option value="Hák" <?php if($select[1] == 'Hák') echo "selected"; ?>>Hák</option>
                <option value="Kleště" <?php if($select[1] == 'Kleště') echo "selected"; ?>>Kleště</option>
                <option value="Konstrukce jih" <?php if($select[1] == 'Konstrukce jih') echo "selected"; ?>>Konstrukce jih</option>
                <option value="Jiné" <?php if($select[1] == 'Jiné') echo "selected"; ?>>Jiné</option>
            </select>
        </div>

        <div class="form-row">
            <div class="form-group col-md-4">
                <label for="text_1">Orientace umístění</label>
                <input type="text" class="form-control" id="text_1" value="<?php echo $text[1]; ?>" placeholder="Zde vyplňte">
            </div>
            <div class="form-group col-md-4">
                <label for="text_2">Orientace umístění</label>
                <input type="text" class="form-control" id="text_2" value="<?php echo $text[2]; ?>" placeholder="Zde vyplňte">
            </div>
            <div class="form-group col-md-4">
                <label for="text_3">Orientace umístění</label>
                <input type="text" class="form-control" id="text_3" value="<?php echo $text[3]; ?>" placeholder="Zde vyplňte">
            </div>
        </div>

        <div class="form-group">
            <label for="text_4">Délka svodu ze střechy k technologii (odhad v m)</label>
            <input type="text" class="form-control" id="text_4" value="<?php echo $text[4]; ?>" placeholder="Zde vyplňte">
        </div>

        <div class="form-group">
            <label for="select_2">Trasa</label>
            <select class="form-control" id="select_2">
                <option value="Vnitřní" <?php if($select[2] == 'Vnitřní') echo "selected"; ?>>Vnitřní</option>
                <option value="Po fasádě" <?php if($select[2] == 'Po fasádě') echo "selected"; ?>>Po fasádě</option>
            </select>
        </div>

        <div class="form-row">
            <div class="form-group col-md-4">
                <label for="select_3">Svod</label>
                <select class="form-control" id="select_3">
                    <option value="Lištou" <?php if($select[3] == 'Lištou') echo "selected"; ?>>Lištou</option>
                    <option value="Chráničkou" <?php if($select[3] == 'Chráničkou') echo "selected"; ?>>Chráničkou</option>
                </select>
            </div>
            <div class="form-group col-md-4">
                <label for="text_5">Délka</label>
                <input type="text" class="form-control" id="text_5" value="<?php echo $text[5]; ?>" placeholder="Zde vyplňte">
            </div>
            <div class="form-group col-md-4">
                <label for="text_6">Barva</label>
                <input type="text" class="form-control" id="text_6" value="<?php echo $text[6]; ?>" placeholder="Zde vyplňte">
            </div>
        </div>

        <div class="form-group">
            <label for="exampleInputEmail1">Formulář byl vyplněn za pomoci telefonické podpory</label>
            <div class="form-check">
                <input type="radio" name="o-radio" id="exampleRadios1" value="s kotvením" <?php if($radio[1] == 's kotvením') echo "checked"; ?>>
                <label for="exampleRadios1">
                    s kotvením
                </label>
            </div>
            <div class="form-check">
                <input type="radio" name="o-radio" id="exampleRadios2" value="bez kotvení" <?php if($radio[1] == 'bez kotvení') echo "checked"; ?>>
                <label for="exampleRadios2">
                    bez kotvení
                </label>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group col-md-4">
                <label for="upload_1" style="display: block;">Foto krovů</label>
                <form id="my_awesome_dropzone_1" class="dropzone" action="#" value="1" id="1">
                    <input type="hidden" id="media_ids_1" value="">
                </form>
            </div>
            <div class="form-group col-md-4">
                <label for="upload_2" style="display: block;">Detailní foto krytiny</label>
                <form id="my_awesome_dropzone_2" class="dropzone" action="#">
                    <input type="hidden" id="media_ids_2" value="">
                </form>
            </div>
            <div class="form-group col-md-4">
                <label for="upload_3" style="display: block;">Foto střech pro umístění panelů</label>
                <form id="my_awesome_dropzone_3" class="dropzone" action="#">
                    <input type="hidden" id="media_ids_3" value="">
                </form>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group col-md-3">
                <label for="text_7">HJ</label>
                <input type="text" class="form-control" id="text_7" value="<?php echo $text[7]; ?>" placeholder="Zde vyplňte">
            </div>
            <div class="form-group col-md-3">
                <label for="text_8">Ampéráž</label>
                <input type="text" class="form-control" id="text_8" value="<?php echo $text[8]; ?>" placeholder="Zde vyplňte">
            </div>
            <div class="form-group col-md-3">
                <label for="text_9">kV</label>
                <input type="text" class="form-control" id="text_9" value="<?php echo $text[9]; ?>" placeholder="Zde vyplňte">
            </div>
            <div class="form-group col-md-3">
                <label for="select_4">Výměna nutná</label>
                <select class="form-control" id="select_4" value="<?php echo $select[4]; ?>">
                    <option value="ne" <?php if($select[4] == 'ne') echo "selected"; ?>>ne</option>
                    <option value="ano" <?php if($select[4] == 'ano') echo "selected"; ?>>ano</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label for="select_5">Místo pro kříž FVE</label>
            <select class="form-control" id="select_5" value="<?php echo $select[5]; ?>">
                <option value="ano" <?php if($select[5] == 'ano') echo "selected"; ?>>ano</option>
                <option value="ne" <?php if($select[5] == 'ne') echo "selected"; ?>>ne</option>
            </select>
        </div>

        <div class="form-group">
            <label for="upload_4" style="display: block;">Foto otevřeného elměr rozvaděče celého, detail HJ</label>
            <form id="my_awesome_dropzone_4" class="dropzone" action="#">
                <input type="hidden" id="media_ids_4" value="">
            </form>
        </div>

        <div class="form-group">
            <label for="text_10">Umístění DR</label>
            <input type="text" class="form-control" id="text_10" value="<?php echo $text[10]; ?>" placeholder="Zde vyplňte">
        </div>

        <div class="form-group">
            <label for="text_11">Umístění technologie</label>
            <input type="text" class="form-control" id="text_11" value="<?php echo $text[11]; ?>" placeholder="Zde vyplňte">
        </div>

        <div class="form-group">
            <label for="text_12">Délka technologické trasy (odhad v m)</label>
            <input type="text" class="form-control" id="text_12" value="<?php echo $text[12]; ?>" placeholder="Zde vyplňte">
        </div>

        <div class="form-group">
            <label for="select_6">Wattrouter</label>
            <select class="form-control" id="select_6">
                <option value="ano" <?php if($select[6] == 'ano') echo "selected"; ?>>ano</option>
                <option value="ne" <?php if($select[6] == 'ne') echo "selected"; ?>>ne</option>
            </select>
        </div>

        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="select_7">Protihořlavá deska do rozvaděče</label>
                <select class="form-control" id="select_7" value="<?php echo $select[7]; ?>">
                    <option value="ano" <?php if($select[7] == 'ano') echo "selected"; ?>>ano</option>
                    <option value="ne" <?php if($select[7] == 'ne') echo "selected"; ?>>ne</option>
                </select>
            </div>
            <div class="form-group col-md-6" style="display: <?php if (empty($text[13])) echo "none"; ?>">
                <label for="text_13">Odhad v m²</label>
                <input type="text" class="form-control" id="text_13" value="<?php echo $text[13]; ?>" placeholder="Zde vyplňte">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group col-md-4">
                <label for="select_8">Trasa zasekat</label>
                <select class="form-control" id="select_8" value="<?php echo $select[8]; ?>">
                    <option value="ano" <?php if($select[8] == 'ano') echo "selected"; ?>>ano</option>
                    <option value="ne" <?php if($select[8] == 'ne') echo "selected"; ?>>ne</option>
                </select>
            </div>
            <div class="form-group col-md-4">
                <label for="select_9">Trasa zasekat</label>
                <select class="form-control" id="select_9" value="<?php echo $select[9]; ?>">
                    <option value="lištou" <?php if($select[9] == 'lištou') echo "selected"; ?>>lištou</option>
                    <option value="chráničkou" <?php if($select[9] == 'chráničkou') echo "selected"; ?>>chráničkoune</option>
                </select>
            </div>
            <div class="form-group col-md-4">
                <label for="text_14">Délka v m</label>
                <input type="text" class="form-control" id="text_14" value="<?php echo $text[14]; ?>" placeholder="Zde vyplňte">
            </div>
        </div>

        <div class="form-group">
            <label for="textarea_1">Speciální požadavky</label>
            <textarea class="form-control" id="textarea_1" rows="5" placeholder="Zde vyplňte"><?php echo $textarea[1]; ?></textarea>
        </div>

        <div class="form-group">
            <label for="textarea_2">Stavební připravenost</label>
            <textarea class="form-control" id="textarea_2" rows="5" placeholder="Zde vyplňte"><?php echo $textarea[2]; ?></textarea>
        </div>

        <div class="form-group">
            <label for="textarea_3">Shrnutí prohlídky</label>
            <textarea class="form-control" id="textarea_3" rows="5" placeholder="Má/nemá klient zájem o realizaci?"><?php echo $textarea[3]; ?></textarea>
        </div>

        <div class="form-row">
            <div class="form-group col-md-3">
                <label for="upload_5" style="display: block;">FOTO DOMU ZE VŠECH SVĚTOVÝCH STRAN</label>
                <form id="my_awesome_dropzone_5" class="dropzone" action="#">
                    <input type="hidden" id="media_ids_5" value="">
                </form>
            </div>
            <div class="form-group col-md-3">
                <label for="upload_6" style="display: block;">FOTO DOMU ZE VŠECH SVĚTOVÝCH STRAN</label>
                <form id="my_awesome_dropzone_6" class="dropzone" action="#">
                    <input type="hidden" id="media_ids_6" value="">
                </form>
            </div>
            <div class="form-group col-md-3">
                <label for="upload_7" style="display: block;">FOTO DOMU ZE VŠECH SVĚTOVÝCH STRAN</label>
                <form id="my_awesome_dropzone_7" class="dropzone" action="#">
                    <input type="hidden" id="media_ids_7" value="">
                </form>
            </div>
            <div class="form-group col-md-3">
                <label for="upload_8" style="display: block;">FOTO DOMU ZE VŠECH SVĚTOVÝCH STRAN</label>
                <form id="my_awesome_dropzone_8" class="dropzone" action="#">
                    <input type="hidden" id="media_ids_8" value="">
                </form>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group col-md-4">
                <label for="upload_9" style="display: block;">FOTO DOMU - ČÍSLO POPISNÉ</label>
                <form id="my_awesome_dropzone_9" class="dropzone" action="#">
                    <input type="hidden" id="media_ids_9" value="">
                </form>
            </div>
            <div class="form-group col-md-4">
                <label for="upload_10" style="display: block;">FOTO ZDROJE TUV</label>
                <form id="my_awesome_dropzone_10" class="dropzone" action="#">
                    <input type="hidden" id="media_ids_10" value="">
                </form>
            </div>
            <div class="form-group col-md-4">
                <label for="upload_11" style="display: block;">FOTO ZDROJE TEPLA</label>
                <form id="my_awesome_dropzone_11" class="dropzone" action="#">
                    <input type="hidden" id="media_ids_11" value="">
                </form>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group col-md-4">
                <label for="upload_12" style="display: block;">VYÚČTOVACÍ FAKTURA ZA EE</label>
                <form id="my_awesome_dropzone_12" class="dropzone" action="#">
                    <input type="hidden" id="media_ids_12" value="">
                </form>
            </div>
            <div class="form-group col-md-4">
                <label for="upload_13" style="display: block;">VYÚČTOVACÍ FAKTURA ZA PLYN</label>
                <form id="my_awesome_dropzone_13" class="dropzone" action="#">
                    <input type="hidden" id="media_ids_13" value="">
                </form>
            </div>
            <div class="form-group col-md-4">
                <label for="number_1">POČET OSOB V DOMÁCNOSTI</label>
                <input type="number" class="form-control" id="number_1" value="<?php echo $number[1]; ?>">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group col-md-4">
                <label for="number_2">VELIKOST VYTÁPĚNÉ PLOCHY [V M²]</label>
                <input type="number" class="form-control" id="number_2" value="<?php echo $number[2]; ?>">
            </div>
            <div class="form-group col-md-4">
                <label for="number_3">STÁŘÍ STŘEŠNÍ KRYTINY [V LETECH]</label>
                <input type="number" class="form-control" id="number_3" value="<?php echo $number[3]; ?>">
            </div>
            <div class="form-group col-md-4">
                <label for="select_13">EXISTENCE NABÍJEČKY ELEKTROMOBILŮ</label>
                <select class="form-control" id="select_13" value="<?php echo $select[13]; ?>">
                    <option value="Ano" <?php if($select[13] == 'Ano') echo "selected"; ?>>ano</option>
                    <option value="Ne" <?php if($select[13] == 'Ne') echo "selected"; ?>>ne</option>
                </select>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group col-md-4">
                <label for="select_11">EXISTENCE KLIMATIZACE</label>
                <select class="form-control" id="select_11" value="<?php echo $select[11]; ?>">
                    <option value="Ano" <?php if($select[11] == 'Ano') echo "selected"; ?>>ano</option>
                    <option value="Ne" <?php if($select[11] == 'Ne') echo "selected"; ?>>ne</option>
                </select>
            </div>
            <div class="form-group col-md-4">
                <label for="select_12">EXISTENCE BAZÉNU</label>
                <select class="form-control" id="select_12" value="<?php echo $select[12]; ?>">
                    <option value="Ano" <?php if($select[12] == 'Ano') echo "selected"; ?>>ano</option>
                    <option value="Ne" <?php if($select[12] == 'Ne') echo "selected"; ?>>ne</option>
                </select>
            </div>
            <div class="form-group col-md-4">
                <label for="text_18">TYP STŘEŠNÍ KRYTINY</label>
                <input type="text" class="form-control" id="text_18" value="<?php echo $text[18]; ?>">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="upload_14" style="display: block;">VYÚČTOVACÍ FAKTURA ZA EE</label>
                <form id="my_awesome_dropzone_14" class="dropzone" action="#">
                    <input type="hidden" id="media_ids_14" value="">
                </form>
            </div>
            <div class="form-group col-md-6">
                <label for="upload_15" style="display: block;">VYÚČTOVACÍ FAKTURA ZA PLYN</label>
                <form id="my_awesome_dropzone_15" class="dropzone" action="#">
                    <input type="hidden" id="media_ids_15" value="">
                </form>
            </div>
        </div>
        </div>
    </div>

    <div class="offset-md-1 col-md-2">
        <div class="form-content" style="margin-top: 86px;">
            <div style="display: flex; align-items: center;">
                <button class="btn btn-primary o-save-btn" style="width: 70%; margin-bottom: 30px; margin-right: 30px;">Aktualizace</button>
                <div id="loading" class="spinner-border text-danger" style="margin-bottom: 30px; display: none;"></div>
            </div>
            <!-- <div>
                <a href="<?php echo esc_url( add_query_arg( array(
                                        'customer_id' => $customer_id,
                                        'post_id' => $post_id,
                                    ), get_permalink( get_page_by_title( 'Obhlídka' ) ) ) ); ?>" class="btn btn-success" style="width: 70%;">View</a>
            </div> -->
        </div>
    </div>
</div>

<script>
var upload_url = <?php echo json_encode($upload_url); ?>;

function getFileSize(url)
{
    var fileSize = '';
    var http = new XMLHttpRequest();
    http.open('HEAD', url, false); // false = Synchronous

    http.send(null); // it will stop here until this http request is complete

    // when we are here, we already have a response, b/c we used Synchronous XHR

    if (http.status === 200) {
        fileSize = http.getResponseHeader('content-length');
    }

    return fileSize;
}

Dropzone.autoDiscover = false;
for (i = 1; i <= 15; i++) {
    var string = "#my_awesome_dropzone_" + i;
    var media = jQuery("#media_ids_" + i);
    jQuery(string).dropzone({
        url: dropParam.upload,
        autoProcessQueue: true,
        uploadMultiple: true,
        parallelUploads: 100,
        maxFiles: 100,
        addRemoveLinks: true,
        dictRemoveFile: "&times",
        dictCancelUpload: "",

        init: function() {
            var myDropzone = this;

            if (upload_url[i]) {
                for (j = 0; j < upload_url[i].length; j++) {
                    if (upload_url[i][j] != "") {
                        var upload_name = upload_url[i][j].split("/");
                        var existingFiles = { name: upload_name[upload_name.length - 1], size: getFileSize(upload_url[i][j]), store: upload_url[i][j] + '^^', };

                        myDropzone.emit("addedfile", existingFiles);
                        myDropzone.emit("thumbnail", existingFiles, upload_url[i][j]);
                        myDropzone.emit("complete", existingFiles);

                        var value = media.val() + upload_url[i][j] + '^^';
                        media.val(value);
                    }
                }
            }

            this.on("success", function(file, response) {
                var id = jQuery(this)[0]["element"][0].id;
                file.previewElement.classList.add("dz-success");
                var value = jQuery("#" + id).val() + response + '^^';
                file.store = response + '^^';
                jQuery("#" + id).val(value);
            });

            this.on("removedfile", function(file) {
                var id = jQuery(this)[0]["element"][0].id;
                var value = $("#" + id).val();
                $("#" + id).val(value.replace(file.store, ''));
                file.previewElement.remove();
            })
        },

        error: function (file, response) {
            file.previewElement.classList.add("dz-error");
        },
    });
}

jQuery(document).on('click', '.o-save-btn', function() {
    jQuery('#loading').show();
    let customer_id = '<?php echo $customer_id; ?>';
    let post_id = '<?php echo $post_id; ?>';

    let text = [], select = [], textarea = [], upload = [], radio = [], date = [], number = [], i;
    for (i = 1; i <= 18; i++) text[i] = jQuery('#text_' + i).val();
    for (i = 1; i <= 13; i++) select[i] = jQuery('#select_' + i).val();
    for (i = 1; i <= 3; i++) textarea[i] = jQuery('#textarea_' + i).val();
    for (i = 1; i <= 15; i++) upload[i] = jQuery('#media_ids_' + i).val();
    for (i = 1; i <= 1; i++) radio[i] = jQuery('input[name="o-radio"]:checked').val();
    for (i = 1; i <= 1; i++) date[i] = jQuery('#date_' + i).val();
    for (i = 1; i <= 3; i++) number[i] = jQuery('#number_' + i).val();

    jQuery.ajax({
        url : "<?php echo esc_url(admin_url('admin-ajax.php')) ?>",
        type : 'post',
        dataType : 'json',
        data : {
            action : 'send_o_form_data',
            customer_id : customer_id,
            post_id : post_id,
            text : text,
            select : select,
            textarea : textarea,
            upload : upload,
            radio : radio,
            date : date,
            number : number,
        },
        success : function( response ) {
            jQuery('#loading').hide();

            window.location.href = '<?php echo admin_url('admin.php?page=obhlidka'); ?>&customer_id=' + response.customer_id + '&post_id=' + response.post_id;
        },
        error: function (error) {
            jQuery('#loading').hide();
            console.log(error);
        }
    });
});
</script>

<?php