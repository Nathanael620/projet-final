import * as popper from '@popperjs/core';
window.popper = popper;

import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Import Bootstrap
import 'bootstrap';
