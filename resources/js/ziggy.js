const Ziggy = {"url":"http:\/\/localhost\/4wheels-chan-ripper.test","port":null,"defaults":{},"routes":{"home":{"uri":"\/","methods":["GET","HEAD"]},"files.index":{"uri":"files","methods":["GET","HEAD"]},"files.massDelete":{"uri":"files\/mass-delete","methods":["DELETE"]},"files.delete":{"uri":"files","methods":["DELETE"]},"files.addToFavorites":{"uri":"files\/add-to-favorites","methods":["POST"]},"fileripper.index":{"uri":"file-ripper","methods":["GET","HEAD"]},"fileripper.rip":{"uri":"file-ripper\/rip","methods":["POST"]},"set-locale":{"uri":"set-locale","methods":["POST"]},"storage.local":{"uri":"storage\/{path}","methods":["GET","HEAD"],"wheres":{"path":".*"},"parameters":["path"]}}};
if (typeof window !== 'undefined' && typeof window.Ziggy !== 'undefined') {
  Object.assign(Ziggy.routes, window.Ziggy.routes);
}
export { Ziggy };
