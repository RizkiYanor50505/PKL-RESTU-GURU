const fs = require('fs');
const code = fs.readFileSync('storage/chk_7.js', 'utf8');
try {
    new Function(code);
    console.log("new Function(code): SUCCESS");
} catch (e) {
    console.log("new Function(code) ERROR:", e.message);
    console.log(e.stack);
}