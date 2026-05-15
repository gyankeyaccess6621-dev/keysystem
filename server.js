const express = require("express");
const bodyParser = require("body-parser");
const cors = require("cors");
const fs = require("fs");

const app = express();

app.use(cors());
app.use(bodyParser.json());
app.use(express.static("public"));

// ================= FILE SYSTEM =================

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

    const users =
    load("users.json");

    const user =
    users.find(
        u =>
        u.username === username
        &&
        u.password === password
    );

    if(!user)
    {
        return res.json({
            status:"invalid"
        });
    }

    res.json({

        status:"success",

        role:user.role,

        username:user.username

    });
});

// ================= CREATE APP =================

app.post("/create-app", (req, res) => {

    const {
        appname
    } = req.body;

    const apps =
    load("apps.json");

    apps.push({
        app: appname
    });

    save(
        "apps.json",
        apps
    );

    res.json({
        status:"success"
    });
});

// ================= GENERATE KEY =================

app.post("/generate-key", (req, res) => {

    const {
        username,
        type,
        apps,
        expiry
    } = req.body;

    const keys =
    load("keys.json");

    const key =
    "GYAN-" +
    Math.random()
    .toString(36)
    .substring(2,10)
    .toUpperCase();

    keys.push({

        username,

        key,

        type,

        apps,

        expiry,

        hwid:"",

        active:false,

        paused:false

    });

    save(
        "keys.json",
        keys
    );

    res.json({

        status:"success",

        key

    });
});

// ================= GET KEYS =================

app.get("/keys", (req, res) => {

    const keys =
    load("keys.json");

    res.json(keys);
});

// ================= DELETE KEY =================

app.post("/delete-key", (req, res) => {

    const {
        key
    } = req.body;

    let keys =
    load("keys.json");

    keys =
    keys.filter(
        k => k.key !== key
    );

    save(
        "keys.json",
        keys
    );

    res.json({
        status:"success"
    });
});

// ================= VERIFY =================

app.get("/verify", (req, res) => {

    const {
        key,
        app,
        hwid
    } = req.query;

    const keys =
    load("keys.json");

    const user =
    keys.find(
        k => k.key === key
    );

    if(!user)
    {
        return res.json({
            status:"invalid"
        });
    }

    if(user.paused)
    {
        return res.json({
            status:"paused"
        });
    }

    if(
        new Date(user.expiry)
        <
        new Date()
    )
    {
        return res.json({
            status:"expired"
        });
    }

    if(
        !user.apps.includes(app)
    )
    {
        return res.json({
            status:"invalid_app"
        });
    }

    if(user.hwid == "")
    {
        user.hwid = hwid;

        save(
            "keys.json",
            keys
        );
    }

    if(user.hwid != hwid)
    {
        return res.json({
            status:"hwid_mismatch"
        });
    }

    user.active = true;

    save(
        "keys.json",
        keys
    );

    res.json({

        status:"success",

        user:user.username,

        expiry:user.expiry,

        type:user.type

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
