const Ziggy = {"url":"http:\/\/localhost\/client-blitz.test","port":null,"defaults":{},"routes":{"home":{"uri":"\/","methods":["GET","HEAD"]},"login":{"uri":"login","methods":["GET","HEAD"]},"auth.login":{"uri":"login","methods":["POST"]},"auth.logout":{"uri":"logout","methods":["POST"]},"registration":{"uri":"registration","methods":["GET","HEAD"]},"auth.registration":{"uri":"registration","methods":["POST"]},"set-locale":{"uri":"set-locale","methods":["POST"]},"storage.local":{"uri":"storage\/{path}","methods":["GET","HEAD"],"wheres":{"path":".*"},"parameters":["path"]}}};
if (typeof window !== 'undefined' && typeof window.Ziggy !== 'undefined') {
  Object.assign(Ziggy.routes, window.Ziggy.routes);
}
export { Ziggy };
