function auto_grow(element) {
    element.style.height = "10px";
    element.style.height = (element.scrollHeight + 20)+"px";
}

jQuery(document).on('show.bs.modal', '#customerModal', async function() {
    $('#c_m_body').children(".comment-div").remove();

    $data_id = jQuery(event.target).closest('tr').attr('data-id');
    jQuery(event.target).closest('tr').find('td').each(function(index, td) {
        if (index == 0) {
            $('#c_m_number').text('Číslo zákazníka: ' + td.innerHTML);
        } else if (index == 2) {
            var tmp = $(this).find('a');
            $('#c_m_title').text(tmp[0].innerHTML);
            $('#c_m_name').text('Název: ' + tmp[0].innerHTML);
        } else if (index == 4) {
            $('#c_m_status').text('Stav: ' + td.innerHTML);
        } else if (index == 6) {
            $('#c_m_responsible_person').text('Odpovědná Osoba: ' + td.innerHTML);
        } else if (index == 7) {
            $('#c_m_email').text('E-mail: ' + td.innerHTML);
        } else if (index == 8) {
            $('#c_m_telephone').text('Telefon: ' + td.innerHTML);
        } else if (index == 9) {
            $('#c_m_region').text('Kraj: ' + td.innerHTML);
        } else if (index == 10) {
            $('#c_m_address').text('Adresa realizace: ' + td.innerHTML);
        } else if (index == 11) {
            $('#c_m_created_date').text('Datum vytvoření: ' + td.innerHTML);
        } else if (index == 12) {
            var termin = new Date(td.innerHTML);
            var today = Date.now();
            if (termin < today) {
                $('#c_m_end_date').css({"color": "#ED1C24"});
            }
            $('#c_m_end_date').text('Termín: ' + td.innerHTML);
        } else if (index == 13) {
            var tmp = $(this).find('a').attr('href');
            $('#c_m_zakaznici').attr('href', tmp);
        } else if (index == 14) {
            var tmp = $(this).find('a').attr('href');
            $('#c_m_formular_poptavky').attr('href', tmp);
        } else if (index == 15) {
            var tmp = $(this).find('a').attr('href');
            $('#c_m_obhlidka').attr('href', tmp);
        } else if (index == 16) {
            var tmp = $(this).find('a').attr('href');
            $('#c_m_nabidky').attr('href', tmp);
        } else if (index == 17) {
            var tmp = $(this).find('div');
            var string = '';
            for (var i = 0; i < tmp.length; i = i + 3) {
                string += '<div class="row comment-div">' + tmp[i].innerHTML + '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' + tmp[i + 1].innerHTML + '</div>';
                const originalString = tmp[i + 2].innerHTML;
                var strippedString = originalString.replace(/(<([^>]+)>)/gi, "");
                string += '<div class="form-group row comment-div"><textarea class="col-sm-12" oninput="auto_grow(this)" readonly>' + strippedString + '</textarea></div>';
            }
            $('#c_m_body').append(string);
            $('.comment-save').attr('data-id', $data_id);
        }
    });
});