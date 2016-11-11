# Pebble Happimeter
###### A COINs Project by University of Bamberg, University of Cologne, and University of Jilin

## API Description
###### A REST API

Preliminary v0 specification. Using Python/Flask at the development server of Rain.

### Response format of failed request

If request **fails**, the response format is as follows:

Name | Type | Description
--- | --- | ---
Status | *int* | 0: fail <br> 1: success
Description | *string* | The brief description of the error


### User
#### Initial Signup
##### [POST] /user/
###### Request Body
Name | Required | Type | Description
--- | --- | --- | ---
DeviceID | yes | *string* | Unique device ID
Mail | yes | *string* | E-Mail

###### Response Body
Name | Type | Description
--- | --- | ---
Status | *int* | Status
Token | *string* | Auth token for the session

#### Login
##### [POST] /auth/
###### Request Body
Name | Type | Description
--- | --- | ---
Mail | *string* | Mail
Password | *string* | Password

###### Response Body
Name | Type | Description
--- | --- | ---
Status | *int* | Status
Token | *string* | Auth token for the session

#### Update Personal Information
##### [POST] /personalinfo/
###### Request Body
Name | Type | Description
--- | --- | ---
Name | *string* | Name
Age | *uint* | Age of the user



### Happimeter
#### Post Data
##### [POST] /happiness/
###### Request Body
Name | Type | Description
--- | --- | ---
Token | *string* | Auth token that identifies the user
Happiness | *uint* | Happiness Likert Scale
WhoHaveYouBeenWith | *uint* | Enum that indicates who has the user been with
DidYouDoSports | *boolean* | Indicates whether the user did any sports activity

###### Response Body
Name | Type | Description
--- | --- | ---
Status | *int* | Status

### Sensor
#### Post Data
##### [POST] /sensor/
###### Request Body
Name | Type | Description
--- | --- | ---
Token | *string* | Auth token that identifies the user
Timestamp | *DateTime* | The DateTime this datasheet is associated with
Steps | *uint* | The number of steps
AvgBPM | *uint* | Average Heart Rate in BPM
MinBPM | *uint* | Minimum Heart Rate in BPM
MaxBPM | *uint* | Maximum Heart Rate in BPM
AvgLightLevel | *uint* | The average ambient light level
Activity | *uint* | The current activity the user is doing
RestingKCalories | *uint* | The burned kilo calories during rest
SleepSeconds | *uint* | The number of seconds the user was sleeping
SleepRestfulSeconds | *uint* | The number of seconds the user was deeply sleeping
ActiveSeconds | *uint* | The number of seconds the user was active
ActiveKCalories | *uint* | The burned kilo calories during activity
WalkedDistanceInMeters | *uint* | The number of meteres the user walked
PositionLat | *Decimal(9,6)* | Current lat position of the user (GPS)
PositionLon | *Decimal(9,6)* | Current lon position of the user (GPS)

###### Response Body
Name | Type | Description
--- | --- | ---
Status | *int* | Status

## Error Code List

Code | Description
--- | ---
General |
10001 | A certain parameter is required, but it can't be found in request.
10002 | Can't save a certain parameter. Maybe the format of the parameter is wrong
User |
20001 | Can't register new account. The database server may not run.
20002 | Can't generate a token. The database server may not run.
20011 | Email is wrong.
20012 | Password is wrong.
Sensor |
30000 | Can't store the sensor data. The database server may not run.
30001 | Can't find the token in database.
