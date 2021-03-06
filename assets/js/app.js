import '../styles/app.css';
import { Routing } from './routes.js';

/* Demo purposes only */
$(".hover").mouseleave(
  function () {
    $(this).removeClass("hover");
  }
);

document.getElementById('links').onclick = function (event) {
  event = event || window.event;
  var target = event.target || event.srcElement,
    link = target.src ? target.parentNode : target,
    options = { index: link, event: event },
    links = this.getElementsByTagName('a');
  blueimp.Gallery(links, options);
};
