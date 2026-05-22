import { FlexJS }       from './core/Flex.js';
import { CallsModel }   from './models/CallsModel.js';
import { callCards }    from './views/callCards.js';
import { runBeforeRender } from './helper/preRenderFn.js';
import { runPostRender }   from './helper/postRenderFn.js';

const apiUrl  = window.FLEX_API_URL || '/api/calls';
const flexApp = new FlexJS(apiUrl);

flexApp.init(
    callCards,
    runPostRender,
    'calls-container',
    CallsModel,
    runBeforeRender,
    false
);
