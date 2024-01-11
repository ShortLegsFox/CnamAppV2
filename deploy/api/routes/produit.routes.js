const { checkJwt}  = require('./jwtMiddleware');

module.exports = app => {
    const produit = require("../controllers/produit.controllers.js");

    var router = require("express").Router();

    router.get('/getcatalog', produit.findAll);

    app.use('/api/produit', router);
};