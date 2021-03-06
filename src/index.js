const express = require("express");
const cors = require("cors");
const app = express();
const morgan = require("morgan");
const swaggerUi = require('swagger-ui-express');
const swaggerDocument = require('../swagger.json');
require("dotenv").config();

//Settings
app.set("port", process.env.PORT || 3000);
app.set("json spaces", 2);
const corsOptions = {
  origin: "http://localhost:4000",
};

//Middleware
app.use(morgan("dev"));
app.use(express.urlencoded({ extended: false }));
app.use(express.json());
app.use(cors(corsOptions));

//Routes
app.use(require("./routes/index"));
require("./routes/auth")(app);
require("./routes/user")(app);
require("./routes/account")(app);
require("./routes/manager")(app);
require("./routes/team")(app);

//Docs
app.use(
  '/api-docs',
  swaggerUi.serve, 
  swaggerUi.setup(swaggerDocument)
);

// Starting the server
app.listen(app.get("port"), () => {
  console.log(`Server listening on port ${app.get("port")}`);
});
