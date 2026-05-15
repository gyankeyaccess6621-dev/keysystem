const express = require("express");
const bodyParser = require("body-parser");
const cors = require("cors");
const fs = require("fs");

const app = express();

app.use(cors());
app.use(bodyParser.json());
app.use(express.static("public"));

function load(file)
{
    if(!fs.existsSync(file))
    {
        fs.writeFileSync(file, "[]");
    }

    return JSON.parse(
        fs.readFileSync(file)
    );
}

function save(file, data)
{
    fs.writeFileSync(
        file,
        JSON.stringify(data, null, 2)
    );
}

// ================= HOME =================

app.get("/", (req, res) => {

    res.sendFile(
        __dirname + "/public/login.html"
    );
});

// ================= LOGIN =================

app.post("/login", (req, res) => {

    const {
        username,
        password
    } = req.body;

    const users = load("users.json");

    const user = users.find(
        u =>
        u.username === username
        &&
        u.password === password
    );

    if(!user)
    {
        return res.json({
            status: "invalid"
        });
    }

    res.json({
        status: "success",
        role: user.role,
        username: user.username
    });
});

// ================= START =================

const PORT =
process.env.PORT || 3000;

app.listen(PORT, () => {

    console.log(
        "SERVER STARTED"
    );
});
