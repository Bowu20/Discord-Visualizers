const http = require("https");
const fs = require("fs");

const TOKEN_GOES_HERE = "ODA0OTI3NDcyMzQ0MzY3MTI0.YBTdzw.qdm1_qKtSsqVIrKOZWMQDR3JY3o";
const OUTPUT_FILE_GOES_HERE = "output.json";


const options = {
    "method": "GET",
    "hostname": "discord.com",
    "port": null,
    "path": "/api/v8/users/@me/guilds",
    "headers": {
        "authority": "discord.com",
        "authorization": TOKEN_GOES_HERE,
        "accept-language": "en-US",
        "user-agent": "Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) discord/0.0.309 Chrome/83.0.4103.122 Electron/9.3.5 Safari/537.36",
        "accept": "*/*",
        "sec-fetch-site": "same-origin",
        "sec-fetch-mode": "cors",
        "sec-fetch-dest": "empty",
        "Content-Length": "0"
    }
};

const req = http.request(options, function (res) {
    const chunks = [];

    res.on("data", function (chunk) {
        chunks.push(chunk);
    });

    res.on("end", function () {
        const body = Buffer.concat(chunks);
        let bodyStr = body.toString();
        fs.writeFileSync(OUTPUT_FILE_GOES_HERE, bodyStr);
        console.log(bodyStr);
    });
});

req.end();