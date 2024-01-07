const { checkJwt}  = require('./jwtMiddleware');

module.exports = app => {
    const utilisateur = require("../controllers/utilisateur.controllers.js");
  
    var router = require("express").Router();

    //Login utilisateur
    router.post("/login", utilisateur.login);

    //Create utilisateur
    router.post("/accountcreation", utilisateur.accountcreation)
  
    app.use('/api/utilisateur', router);
  };
