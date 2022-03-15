jQuery(document).on('show.bs.modal', '#customerModal', function() {
    jQuery(event.target).closest('tr').find('td').each(function(index, td) {
        if (index == 0) {
            $('#c_m_number').text('Číslo zákazníka: ' + td.innerHTML);
        } else if (index == 1) {
            var tmp = $(this).find('a');
            $('#c_m_name').text('Název: ' + tmp[0].innerText);
        } else if (index == 3) {
            $('#c_m_status').text('Stav: ' + td.innerHTML);
        } else if (index == 5) {
            $('#c_m_responsible_person').text('Odpovědná Osoba: ' + td.innerHTML);
        } else if (index == 6) {
            $('#c_m_email').text('E-mail: ' + td.innerHTML);
        } else if (index == 7) {
            $('#c_m_telephone').text('Telefon: ' + td.innerHTML);
        } else if (index == 8) {
            $('#c_m_region').text('Kraj: ' + td.innerHTML);
        } else if (index == 9) {
            $('#c_m_address').text('Adresa realizace: ' + td.innerHTML);
        } else if (index == 11) {
            var tmp1 = $(this).find('input').attr('value');
            var tmp2 = $(this).find('input').attr('data-id');
            $('#c_m_created_date').attr('value', tmp1 ? tmp1 : "");
            $('#c_m_created_date').attr('data-id', tmp2);
        } else if (index == 12) {
            var tmp = $(this).find('a').attr('href');
            $('#c_m_zakaznici').attr('href', tmp);
        } else if (index == 13) {
            var tmp = $(this).find('a').attr('href');
            $('#c_m_formular_poptavky').attr('href', tmp);
        } else if (index == 14) {
            var tmp = $(this).find('a').attr('href');
            $('#c_m_obhlidka').attr('href', tmp);
        } else if (index == 15) {
            var tmp = $(this).find('a').attr('href');
            $('#c_m_nabidky').attr('href', tmp);
        }
    });
});