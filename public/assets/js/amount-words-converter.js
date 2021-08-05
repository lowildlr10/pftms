// Output variable
let output_words = '';

// System for American Numbering
const th_val = ['', 'Thousand', 'Million', 'Billion', 'Trillion'];
// System for uncomment this line for Number of English
// var th_val = ['','thousand','million', 'milliard','billion'];

const dg_val = ['Zero', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine'];
const tn_val = ['Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'];
const tw_val = ['Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];
function toWordsconvert(s) {
    s = s.toString();
    s = s.replace(/[\, ]/g, '');
    if (s != parseFloat(s))
        return 'not a number ';
    var x_val = s.indexOf('.');
    if (x_val == -1)
        x_val = s.length;
    if (x_val > 15)
        return 'too big';
    var n_val = s.split('');
    var str_val = '';
    var sk_val = 0;
    for (var i = 0; i < x_val; i++) {
        if ((x_val - i) % 3 == 2) {
            if (n_val[i] == '1') {
                str_val += tn_val[Number(n_val[i + 1])] + ' ';
                i++;
                sk_val = 1;
            } else if (n_val[i] != 0) {
                str_val += tw_val[n_val[i] - 2] + ' ';
                sk_val = 1;
            }
        } else if (n_val[i] != 0) {
            str_val += dg_val[n_val[i]] + ' ';
            if ((x_val - i) % 3 == 0)
                str_val += 'Hundred ';
            sk_val = 1;
        }
        if ((x_val - i) % 3 == 1) {
            if (sk_val)
                str_val += th_val[(x_val - i - 1) / 3] + ' ';
            sk_val = 0;
        }
    }

    output_words = str_val.replace(/\s+/g, ' ') + 'Pesos';

    if (x_val != s.length) {
        /*
        var y_val = s.length;
        for (var i = x_val + 1; i < y_val; i++)
            output_words += dg_val[n_val[i]] + ' ';*/

        n_val = s.split('.')[1];

        if (parseFloat(n_val)) {
            let divisor = '1';

            for (let i = 0; i < n_val.length; i++) {
                divisor += '0';
            }

            output_words += ` and ${n_val}/${divisor} Centavos`;
        }
    }
    return `${output_words} Only`;
}
