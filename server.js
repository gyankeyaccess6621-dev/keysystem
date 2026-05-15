const express = require("express");
const app = express();

app.get("/", (req, res) => {
    res.send("KEYSYSTEM WORKING");
});

app.get("/check.php", (req, res) => {
    res.json({
        status: "success",
        user: "GYAN",
        expiry: "2099-12-31"
    });
});

const PORT = process.env.PORT || 3000;

app.listen(PORT, () => {
    console.log("Server running");
});
