const jwtAuth = require("../jwtAuth");

module.exports = (app) => {
  const user = require("../../controllers/user");
  const router = require("express").Router();
  
  jwtAuth(router);

  router.post("/", user.create);
  router.get("/", user.findAll);
  router.put("/", user.update);
  router.delete("/:email", user.delete);

  app.use("/api/user", router);
};
