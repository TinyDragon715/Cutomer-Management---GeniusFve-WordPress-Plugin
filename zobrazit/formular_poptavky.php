<?php
$customer_id = filter_input( INPUT_GET, "customer_id", FILTER_SANITIZE_STRING );
$post_id     = filter_input( INPUT_GET, "post_id", FILTER_SANITIZE_STRING );
$meta = get_post_meta($post_id);

$args = array(
    'post_type' => 'zakaznik',
    'post_status' => 'publish',
    'post__in' => array($customer_id),
);
$post = get_posts($args);
$customer_meta = get_post_meta($customer_id);
$email = $customer_meta['e-mail'][0];
$telefon = $customer_meta['telefon'][0];
$adresa_realizace = $customer_meta['adresa_realizace'][0];
$trvaly_pobyt = $customer_meta['trvaly_pobyt'][0];
$title = explode(' ', $post[0]->post_title, 2);

?>
<div class="row">
    <div class="offset-md-2 col-md-6">
        <h1>Formulář poptávky</h1>
        <div class="form-content">
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="_field_9">Jméno *</label>
                    <input type="text" class="form-control" id="_field_9" value="<?php echo $title[0]; ?>" <?php echo $customer_id ? 'disabled' : '' ?>>
                    <label for="_field_9" style="color: red; display: none;" class="validate_message">This field is required.</label>
                </div>
                <div class="form-group col-md-6">
                    <label for="_field_10">Příjmení *</label>
                    <input type="text" class="form-control" id="_field_10" value="<?php echo $title[1]; ?>" <?php echo $customer_id ? 'disabled' : '' ?>>
                    <label for="_field_10" style="color: red; display: none;" class="validate_message">This field is required.</label>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="_field_18">Email *</label>
                    <input type="email" class="form-control" id="_field_18" value="<?php echo $email ?>" <?php echo $customer_id ? 'disabled' : '' ?>>
                    <label for="_field_18" style="color: red; display: none;" class="validate_message">This field is required.</label>
                </div>
                <div class="form-group col-md-6">
                    <label for="_field_11">Telefon</label>
                    <input type="text" class="form-control" id="_field_11" value="<?php echo $telefon ?>" <?php echo $customer_id ? 'disabled' : '' ?>>
                </div>
            </div>

            <div class="form-group">
                <label for="_field_63">Vyberte svou kraj. *</label>
                <select class="form-control" id="_field_63" value="<?php echo $meta['_field_63'][0]; ?>">
                    <?php
                    for ($i = 1; $i <= count($GLOBALS["kraj_arr"]); $i++) {
                        echo "<option value='" . $GLOBALS["kraj_arr"][$i - 1]["wrong"] . "'";
                        if ($meta['_field_63'][0] == $GLOBALS["kraj_arr"][$i - 1]["wrong"]) echo " selected";
                        echo ">" . $GLOBALS["kraj_arr"][$i - 1]["right"] . "</option>";
                    }
                    ?>
                </select>
                <label for="_field_63" style="color: red; display: none;" class="validate_message">This field is required.</label>
            </div>

            <div class="form-row">
                <div class="form-group col-md-12">
                    <label for="_field_65">Trvalý Pobyt</label>
                    <input type="text" class="form-control" id="_field_65" value="<?php echo $trvaly_pobyt ?>" <?php echo $customer_id ? 'disabled' : '' ?>>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="_field_12">Přesná adresa instalace *</label>
                    <input type="text" class="form-control" id="_field_12" value="<?php echo $meta['_field_12'][0]; ?>">
                    <label for="_field_12" style="color: red; display: none;" class="validate_message">This field is required.</label>
                </div>
                <div class="form-group col-md-6">
                    <label for="_field_13">Druh nemovitosti *</label>
                    <select class="form-control" id="_field_13" value="<?php echo $meta['_field_13'][0]; ?>">
                        <option value="rodinny_dum" <?php if ($meta['_field_13'][0] == 'rodinny_dum') echo "selected"; ?>>Rodinný dům</option>
                        <option value="chata" <?php if ($meta['_field_13'][0] == 'chata') echo "selected"; ?>>Chata</option>
                        <option value="firma" <?php if ($meta['_field_13'][0] == 'firma') echo "selected"; ?>>Firma</option>
                        <option value="jine" <?php if ($meta['_field_13'][0] == 'jine') echo "selected"; ?>>Jiné</option>
                    </select>
                    <label for="_field_13" style="color: red; display: none;" class="validate_message">This field is required.</label>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="_field_15">Spotřeba domu [MWh/rok] *</label>
                    <input type="text" class="form-control" id="_field_15" value="<?php echo $meta['_field_15'][0]; ?>">
                    <label for="_field_15" style="color: red; display: none;" class="validate_message">This field is required.</label>
                </div>
                <div class="form-group col-md-6">
                    <label for="_field_16">Spotřeba domu [Kč/měsíc] *</label>
                    <input type="text" class="form-control" id="_field_16" value="<?php echo $meta['_field_16'][0]; ?>">
                    <label for="_field_16" style="color: red; display: none;" class="validate_message">This field is required.</label>
                </div>
            </div>

            <?php
                $match_value = ['plyn', 'elektrina', 'jine'];
                $flag = [0, 0, 0];

                $data = unserialize($meta['_field_34'][0]);
                
                if ($data) {
                    for ($i = 0; $i < count($data); $i++) {
                        for ($j = 0; $j < count($match_value); $j++) {
                            if ($data[$i] == $match_value[$j]) {
                                $flag[$j] = 1;
                            }
                        }
                    }
                }
            ?>
            <div class="form-row">
                <div class="form-group">
                    <label for="_field_34">Způsob vytápění *</label>
                    <div id="_field_34">
                        <div class="form-check">
                            <input type="checkbox" id="plyn" <?php if($flag[0]) echo 'checked'; ?>>
                            <label for="plyn">Plyn</label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" id="elektrina" <?php if($flag[1]) echo 'checked'; ?>>
                            <label for="elektrina">Elektřina</label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" id="jine" <?php if($flag[2]) echo 'checked'; ?>>
                            <label for="jine">Jiné</label>
                        </div>
                    </div>
                    <label for="_field_34" style="color: red; display: none;" class="validate_message">This field is required.</label>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="_field_35">TČ [kWh] *</label>
                    <input type="text" class="form-control" id="_field_35" value="<?php echo $meta['_field_35'][0]; ?>">
                    <label for="_field_35" style="color: red; display: none;" class="validate_message">This field is required.</label>
                </div>
                <div class="form-group col-md-4">
                    <label for="_field_37">Elektrokotel [kWh] *</label>
                    <input type="text" class="form-control" id="_field_37" value="<?php echo $meta['_field_37'][0]; ?>">
                    <label for="_field_37" style="color: red; display: none;" class="validate_message">This field is required.</label>
                </div>
                <div class="form-group col-md-4">
                    <label for="_field_38">Pevná paliva *</label>
                    <input type="text" class="form-control" id="_field_38" value="<?php echo $meta['_field_38'][0]; ?>">
                    <label for="_field_38" style="color: red; display: none;" class="validate_message">This field is required.</label>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="_field_39">Přímotopy [kWh] *</label>
                    <input type="text" class="form-control" id="_field_39" value="<?php echo $meta['_field_39'][0]; ?>">
                    <label for="_field_39" style="color: red; display: none;" class="validate_message">This field is required.</label>
                </div>
                <div class="form-group col-md-4">
                    <label for="_field_40">Jiné</label>
                    <input type="text" class="form-control" id="_field_40" value="<?php echo $meta['_field_40'][0]; ?>">
                </div>
            </div>

            <div class="form-group">
                <label for="_field_42">Kombinace? Uveďte níže.</label>
                <input type="text" class="form-control" id="_field_42" value="<?php echo $meta['_field_42'][0]; ?>" placeholder="Např. elektřina + plyn">
            </div>

            <div class="form-group">
                <label for="_field_30">Způsob ohřevu *</label>
                <select class="form-control" id="_field_30" value="<?php echo $meta['_field_30'][0]; ?>">
                    <option value="play" <?php if ($meta['_field_30'][0] == 'play') echo "selected"; ?>>Plyn</option>
                    <option value="elektrina" <?php if ($meta['_field_30'][0] == 'elektrina') echo "selected"; ?>>Elektřina</option>
                    <option value="termika" <?php if ($meta['_field_30'][0] == 'termika') echo "selected"; ?>>Termika</option>
                    <option value="tuha_paliva" <?php if ($meta['_field_30'][0] == 'tuha_paliva') echo "selected"; ?>>Tuhá paliva</option>
                    <option value="kombinace" <?php if ($meta['_field_30'][0] == 'kombinace') echo "selected"; ?>>Kombinace</option>
                </select>
                <label for="_field_30" style="color: red; display: none;" class="validate_message">This field is required.</label>
            </div>

            <div class="form-group">
                <label for="_field_41">Bojler</label>
                <input type="text" class="form-control" id="_field_41" value="<?php echo $meta['_field_41'][0]; ?>">
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="_field_21">Materiál *</label>
                    <select class="form-control" id="_field_21" value="<?php echo $meta['_field_21'][0]; ?>">
                        <option value="taska" <?php if ($meta['_field_21'][0] == 'taska') echo "selected"; ?>>Taška</option>
                        <option value="sindel" <?php if ($meta['_field_21'][0] == 'sindel') echo "selected"; ?>>Šindel</option>
                        <option value="plech_falcovy" <?php if ($meta['_field_21'][0] == 'plech_falcovy') echo "selected"; ?>>Plech - falcový</option>
                        <option value="plech_sablony" <?php if ($meta['_field_21'][0] == 'plech_sablony') echo "selected"; ?>>Plech - šablony</option>
                        <option value="eternit_vlnity" <?php if ($meta['_field_21'][0] == 'eternit_vlnity') echo "selected"; ?>>Eternit - vlnitý</option>
                        <option value="eternit_sablony" <?php if ($meta['_field_21'][0] == 'eternit_sablony') echo "selected"; ?>>Eternit - šablony</option>
                        <option value="gembrit" <?php if ($meta['_field_21'][0] == 'gembrit') echo "selected"; ?>>Gembrit</option>
                        <option value="rsb" <?php if ($meta['_field_21'][0] == 'rsb') echo "selected"; ?>>Rovná střecha betonová (překlady, armování)</option>
                        <option value="rsn" <?php if ($meta['_field_21'][0] == 'rsn') echo "selected"; ?>>Rovná střecha nenosná</option>
                    </select>
                    <label for="_field_21" style="color: red; display: none;" class="validate_message">This field is required.</label>
                </div>
                <div class="form-group col-md-6">
                    <label for="_field_22">Umístění *</label>
                    <select class="form-control" id="_field_22" value="<?php echo $meta['_field_22'][0]; ?>">
                        <option value="rd" <?php if ($meta['_field_22'][0] == 'rd') echo "selected"; ?>>Rodinný dům</option>
                        <option value="garaz" <?php if ($meta['_field_22'][0] == 'garaz') echo "selected"; ?>>Garáž</option>
                        <option value="pergola" <?php if ($meta['_field_22'][0] == 'pergola') echo "selected"; ?>>Pergola</option>
                        <option value="jine" <?php if ($meta['_field_22'][0] == 'jine') echo "selected"; ?>>Jiné</option>
                    </select>
                    <label for="_field_22" style="color: red; display: none;" class="validate_message">This field is required.</label>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="_field_24">Rozměr střechy pro panely [m x m] *</label>
                    <input type="text" class="form-control" id="_field_24" value="<?php echo $meta['_field_24'][0]; ?>" placeholder="Např. 10 x 8 m">
                    <label for="_field_24" style="color: red; display: none;" class="validate_message">This field is required.</label>
                </div>
                <div class="form-group col-md-6">
                    <label for="_field_26">Orientace (ve stupních, popř. J, JZ, JV, V, Z) *</label>
                    <input type="text" class="form-control" id="_field_26" value="<?php echo $meta['_field_26'][0]; ?>">
                    <label for="_field_26" style="color: red; display: none;" class="validate_message">This field is required.</label>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="_field_44">Klimatizace</label>
                    <input type="text" class="form-control" id="_field_44" value="<?php echo $meta['_field_44'][0]; ?>" placeholder="Zadejte počet ks a příkon">
                </div>
                <div class="form-group col-md-4">
                    <label for="_field_45">Akvária, terária</label>
                    <input type="text" class="form-control" id="_field_45" value="<?php echo $meta['_field_45'][0]; ?>" placeholder="Zadejte počet ks a příkon">
                </div>
                <div class="form-group col-md-4">
                    <label for="_field_46">Vířivka</label>
                    <input type="text" class="form-control" id="_field_46" value="<?php echo $meta['_field_46'][0]; ?>" placeholder="Zadejte počet ks a příkon">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="_field_47">Sauna</label>
                    <input type="text" class="form-control" id="_field_47" value="<?php echo $meta['_field_47'][0]; ?>" placeholder="Zadejte počet ks a příkon">
                </div>
                <div class="form-group col-md-4">
                    <label for="_field_48">Bazén s filtrací</label>
                    <input type="text" class="form-control" id="_field_48" value="<?php echo $meta['_field_48'][0]; ?>" placeholder="Zadejte počet ks a příkon">
                </div>
                <div class="form-group col-md-4">
                    <label for="_field_49">Bazén s ohřevem a filtrací</label>
                    <input type="text" class="form-control" id="_field_49" value="<?php echo $meta['_field_49'][0]; ?>" placeholder="Zadejte počet ks a příkon">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="_field_50">Protiproud</label>
                    <input type="text" class="form-control" id="_field_50" value="<?php echo $meta['_field_50'][0]; ?>" placeholder="Zadejte počet ks a příkon">
                </div>
                <div class="form-group col-md-4">
                    <label for="_field_51">Rekuperace</label>
                    <input type="text" class="form-control" id="_field_51" value="<?php echo $meta['_field_51'][0]; ?>" placeholder="Zadejte počet ks a příkon">
                </div>
                <div class="form-group col-md-4">
                    <label for="_field_52">Točivé motory 3F</label>
                    <input type="text" class="form-control" id="_field_52" value="<?php echo $meta['_field_52'][0]; ?>" placeholder="Zadejte počet ks a příkon">
                </div>
            </div>

            <div class="form-group">
                <label for="_field_23">Doplňkové požadavky na FVE</label>
                <textarea class="form-control" id="_field_23" rows="10"><?php echo $meta['_field_23'][0]; ?></textarea>
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="exampleInputEmail1">Formulář byl vyplněn za pomoci telefonické podpory</label>
                    <div class="form-check">
                        <input type="radio" name="f-radio" id="exampleRadios1" value="ano" <?php if ($meta['_field_57'][0] == 'ano') echo "checked"; ?>>
                        <label for="exampleRadios1">
                            Ano
                        </label>
                    </div>
                    <div class="form-check">
                        <input type="radio" name="f-radio" id="exampleRadios1" value="ne" <?php if ($meta['_field_57'][0] == 'ne') echo "checked"; ?>>
                        <label for="exampleRadios1">
                            Ne
                        </label>
                    </div>
                </div>
                <div class="form-group col-md-6">
                    <label for="_field_58">O společnosti Genius FVE jsem se dozvěděl přes:</label>
                    <select class="form-control" id="_field_58" value="<?php echo $meta['_field_58'][0]; ?>">
                        <option value="doporuceni" <?php if ($meta['_field_58'][0] == 'doporuceni') echo "selected"; ?>>Doporučení</option>
                        <option value="google" <?php if ($meta['_field_58'][0] == 'google') echo "selected"; ?>>Google</option>
                        <option value="seznam" <?php if ($meta['_field_58'][0] == 'seznam') echo "selected"; ?>>Seznam</option>
                        <option value="tel_nabidka" <?php if ($meta['_field_58'][0] == 'tel_nabidka') echo "selected"; ?>>Telefonickou nabídku</option>
                        <option value="elkov" <?php if ($meta['_field_58'][0] == 'elkov') echo "selected"; ?>>Elkov</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="offset-md-1 col-md-2">
        <div class="form-content" style="margin-top: 86px; text-align: left;">
            <div style="display: flex; align-items: center;">
                <button class="btn btn-primary f-save-btn" style="width: 70%; margin-bottom: 30px; margin-right: 30px;">Aktualizace</button>
                <div id="loading" class="spinner-border text-danger" style="margin-bottom: 30px; display: none;"></div>
            </div>
        </div>
    </div>
</div>

<script>
jQuery(document).on('click', '.f-save-btn', function() {
    // Hide all validate message
    jQuery('.validate_message').hide();



    // Define and initialize variables
    let customer_id = '<?php echo $customer_id; ?>';
    let post_id = '<?php echo $post_id; ?>';
    let text = [], select = [], textarea = [], radio = [], checkbox = [], i, validateTmpValue, validateFlag = false;
    
    let index_arr = [9, 10, 18, 11, 12, 15, 16, 35, 37, 38, 39, 40, 41, 42, 24, 26, 44, 45, 46, 47, 48, 49, 50, 51, 52, 65];
    for (i = 0; i < index_arr.length; i++) {
        text[i] = jQuery('#_field_' + index_arr[i]).val();
    }
    index_arr = [13, 30, 21, 22, 58, 63];
    for (i = 0; i < index_arr.length; i++) {
        select[i] = jQuery('#_field_' + index_arr[i]).val();
    }
    textarea[0] = jQuery('#_field_23').val();
    radio[0]    = jQuery('input[name="f-radio"]:checked').val();
    checkbox[0] = (jQuery('#plyn').is(":checked") ? 1 : 0) * 1 + (jQuery('#elektrina').is(":checked") ? 1 : 0) * 2 + (jQuery('#jine').is(":checked") ? 1 : 0) * 4;



    // Validate inputs field
    let validate_index_arr = [9, 10, 18, 63, 12, 13, 15, 16, 35, 37, 38, 39, 30, 21, 22, 24, 26];
    for (i = 0; i < validate_index_arr.length; i++) {
        validateTmpValue = jQuery('#_field_' + validate_index_arr[i]).val();
        if (!validateTmpValue) {
            // Show validate message
            jQuery('#_field_' + validate_index_arr[i]).parent().find('.validate_message').show();
            // Set validateFlag to true
            validateFlag = true;
        }
    }

    // Validate checkbox
    if (checkbox[0] == 0) {
        // Show validate message
        jQuery('#_field_34').parent().find('.validate_message').show();
        // Set validateFlag to true
        validateFlag = true;
    }

    // Check validateFlag
    if (validateFlag) {
        // Do not send ajax
        return;
    }



    // Show loading gif
    jQuery('#loading').show();  

    jQuery.ajax({
        url : "<?php echo esc_url(admin_url('admin-ajax.php')) ?>",
        type : 'post',
        dataType : 'json',
        data : {
            action : 'send_f_form_data',
            customer_id : customer_id,
            post_id : post_id,
            text : text,
            select : select,
            textarea : textarea,
            radio : radio,
            checkbox : checkbox,
        },
        success : function( response ) {
            jQuery('#loading').hide();

            window.location.href = '<?php echo admin_url('admin.php?page=formular_poptavky'); ?>&customer_id=' + response.customer_id + '&post_id=' + response.post_id;
        },
        error: function (error) {
            jQuery('#loading').hide();
            console.log(error);
        }
    });
});
</script>