# Pebble Happimeter
###### A COINs Project by University of Bamberg, University of Cologne, and University of Jilin

Our mind feels and our body senses when we are happy – or unhappy. Knowing when we are happy, and telling others about it will make them and us happier.

*Goal*: Build a body sensing system that measures and visualizes our happiness. It will consist of a Smartwatch or Android (later also iPhone) App that polls us 5-7 times per day to collect our emotional states. At the same time a Pebble smartwatch will collect a variety of body readings (temperature, light, steps, sleep, speech, energy,…).

*Research Hypothesis*: Comparing Pebble sensor readings with emotional state will allow us to correlate mood state with Pebble readings to automatically calculate mood state from Pebble sensor readings.

## Data Specification
###### Relational Data Structure (e.g. using MySQL)

### User
A table that stores information about every user using the app.

Name | Type | Description
--- | --- | ---
ID | *uint* | Unique ID of the user (auto increment)
Mail | *string* | Unique mail
Password | *string* | Password of the user (Encrypted/Hashed)
Salt | *string* | Salt that do encrypto password with
Name | *string* | Name
Age | *uint* | Age of the user
Avatar | *string* |URL to the users avatar/picture
SignupAt | *DateTime* | DateTime when the user was created
LastLogin | *DateTime* | Last time the user logged in

### UserInfo
A table that stores some changeable information of users.

Name | Type | Description
--- | --- | ---
ID | *uint* | Unique ID of the user (auto increment)
UserID | *uint* | ID of the user
Weight | *double* | Weight in kg of the user
Sportiness | *uint* | Sportiness of the user (low, medium, high)
updatetime | *DateTime* | Time the user update these information

### Device
A table that stores device information

Name | Type | Description
--- | --- | ---
ID | *uint* | Unique ID of the user (auto increment)
DeviceID | *string* | Unique device ID
UserID | *uint* | ID of the user

### Auth
A table that stores auth sessions and their assocations to the user.

Name | Type | Description
--- | --- | ---
ID | *uint* | Unique ID of the auth entry (auto increment)
UserID | *uint* | ID of the user
Token | *string* | Random GUID for the session
Created | *DateTime* | DateTime when the session was created
IsDestroy | *int* | 0:used <br /> 1:destory

### Happiness Data
A table that stores all happiness data associated to an user.

Name | Type | Description
--- | --- | ---
ID | *uint* | Unique ID of the dataset
UserID | *unit* | Unique ID of the user associated with this dataset
Happiness | *uint* | Happiness Likert Scale
WhoHaveYouBeenWith | *uint* | Enum that indicates who has the user been with
DidYouDoSports | *boolean* | Indicates whether the user did any sports activity

### Sensor Data
A table that stores all measuring data associated to an user.

Name | Type | Description
--- | --- | ---
ID | *uint* | Unique ID of the dataset
UserID | *unit* | Unique ID of the user associated with this dataset
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
