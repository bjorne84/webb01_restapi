# RestAPI - portfolio

## Overview
A RestAPI with portfoliodata, with HTTP-METHODS:  
GET, POST, PUT and DELETE you can access and retrive data from the mysql-database and get it in JSON-format.

### GET
You can access data from the these tables:  
- courses, se which courses I have taken and from what school etc. Languages: https://webb01.se/restapi/?table=language 
    - By adding a id you choose courses from a specific school. ID 1 or 2, like: https://webb01.se/restapi/?table=courses&id=2
- CV from the work_experience table: https://webb01.se/restapi/?table=work_experience
    - add id to get a scecfic cv item.
- Portfolio, data over my portfolio: https://webb01.se/restapi/?table=portfolio 
    - add id to get a scecfic portfolioproject
- Languages: https://webb01.se/restapi/?table=language
    - add id to get only a specific languages.   


### POST
By sending a json-file with the HTTP-method post to url: https://webb01.se/restapi  
you can add a new post to each table. The correct format: 
{
  "Table": "courses",
  "New": true,
  "Indata": {
    "Education_ID": 2,
    "CourseName": "Databaser",
    "Points`": "7,5 HP",
    "Grade": "C",
    "Languages_id": [2, 3,]
  }
}