/*Must load first*/
import './bootstrap';

/*Project related stuff*/
import '@coreui/coreui'
import 'select2/dist/js/select2.full.min'
import 'pc-bootstrap4-datetimepicker'
import 'bootstrap4-toggle/js/bootstrap4-toggle.min'
import 'print-this/printThis';

import 'jszip'
import * as pdfMake from 'pdfmake/build/pdfmake.js'
import * as pdfFonts from 'pdfmake/build/vfs_fonts';

pdfMake.vfs = pdfFonts.pdfMake.vfs;

//import * as dt from 'datatables.net';
import * as dt_bs from 'datatables.net-bs4';
import 'datatables.net-buttons-bs4'
import 'datatables.net-autofill-bs4'
import 'datatables.net-buttons/js/dataTables.buttons.js'
import 'datatables.net-buttons/js/buttons.colVis.js'
import 'datatables.net-buttons/js/buttons.flash.js'
import 'datatables.net-buttons/js/buttons.html5.js'
import 'datatables.net-buttons/js/buttons.print.js'
import 'datatables.net-colreorder-bs4'
import 'datatables.net-fixedcolumns-bs4'
import 'datatables.net-fixedheader-bs4'
import 'datatables.net-keytable-bs4'
import 'datatables.net-responsive-bs4'
import 'datatables.net-rowgroup-bs4'
import 'datatables.net-rowreorder-bs4'
import 'datatables.net-scroller-bs4'
import 'datatables.net-searchpanes-bs4'
import 'datatables.net-select-bs4'

dt_bs(window, $);
window.JSZip = require('jszip');