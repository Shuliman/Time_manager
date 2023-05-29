# Time_manager

This is a time manager created with php
It allows users to track and manage their time spent on projects or learning activities. The application calculates the total time spent on a project/learning activity over all time and provides a summary for the last week.


## Install Guide:
`git clone https://github.com/Shuliman/Time_manager.git`
`install composer reqiuerments`
create `time_manager` DB
create table using:
`CREATE TABLE your_table_name (
  day DATE NULL,
  time_on_project FLOAT NULL,
  time_on_learning FLOAT NULL,
  PRIMARY KEY (day)
);`
You need to configure your server where the app will be running