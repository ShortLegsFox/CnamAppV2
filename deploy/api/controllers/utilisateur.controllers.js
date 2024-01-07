const {v4: uuidv4} = require("uuid");
const {ACCESS_TOKEN_SECRET} = require("../config.js");

const jwt = require('jsonwebtoken');
const db = require("../models");
const Utilisateur = db.utilisateur;
const Op = db.Sequelize.Op;

function generateAccessToken(user) {
    return jwt.sign(user, ACCESS_TOKEN_SECRET, {expiresIn: '1800s'});
}

exports.login = (req, res) => {
    const utilisateur = {
        login: req.body.login,
        password: req.body.password
    };

    Utilisateur.findOne({ where: { login: utilisateur.login } })
        .then(data => {

                if (data.password === utilisateur.password) {
                    const user = {
                        id: data.id,
                        nom: data.nom,
                        prenom: data.prenom,
                        email: data.email,
                    };

                    let accessToken = generateAccessToken(user);
                    res.setHeader('Authorization', `Bearer ${accessToken}`);
                    user.token = accessToken;

                    console.log (accessToken);

                    res.send(user);
                }
                else{
                    res.status(401).send({
                        message: "Mot de passe incorrect"
                    });
                }
            }
        )
        .catch(err => {
            res.status(500).send({
                message: "Nom d'utilisateur ne figure pas dans la base"
            });
        });
};

exports.accountcreation = (req, res) => {
    const newUtilisateur = {
        nom: req.body.nom,
        prenom: req.body.prenom,
        adresse: req.body.adresse,
        codepostal: req.body.codepostal,
        ville: req.body.ville,
        sexe: req.body.sexe,
        telephone: req.body.telephone,
        email: req.body.email,
        login: req.body.login,
        password: req.body.password
    };

    console.log(req.body);

    Utilisateur.findOne({ where: { login: newUtilisateur.login } })
        .then(data => {
            if (data) {
                res.status(401).send({
                    message: "Nom d'utilisateur déjà utilisé!"
                });
            }
            else{
                Utilisateur.create(newUtilisateur)
                    .then(data => {
                        const user = {
                            nom: data.nom,
                            prenom: data.prenom,
                            adresse: data.adresse,
                            codepostal: data.codepostal,
                            ville: data.ville,
                            sexe: data.sexe,
                            telephone: data.telephone,
                            email: data.email,
                            login: data.login,
                            password: data.password
                        };

                        let accessToken = generateAccessToken(user);
                        res.setHeader('Authorization', `Bearer ${accessToken}`);
                        user.token = accessToken;

                        console.log (accessToken);
                        res.send(user);
                    })
                    .catch(err => {
                        res.status(500).send({
                            message: err.message || "Une erreur s'est produite lors de la création de l'utilisateur."
                        });
                    });
            }
        })
        .catch(err => {
                res.status(500).send({
                    message: "Nom d'utilisateur déjà utilisé!"
                });
            }
        );
};
