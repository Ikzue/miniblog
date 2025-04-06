import './bootstrap';

import Alpine from 'alpinejs';
import axios from 'axios';
import {addTableData} from './table-helpers.js';

window.Alpine = Alpine;

Alpine.start();

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

window.addTableData = addTableData;
