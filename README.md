----------------------------------------------------------------Methods---------------------------------------------------------
GET:
1. /booking
URL Encoding Method call

Input Params:
1. uuid : Unique User ID
2. from_station : From station
3. to_station: To station
4. date_time: 20220615090105; Format: YYYYMMDDhhmmss
5. class: Class as 1st or 2nd; valid input: 1 or 2
6. no_of_passenger: Number of passengers
7. special_service_flag: Special Service Flag
8. seats: Number of seats to reserve
9. food_service: Food Service required
10. luggage_service: Luggage Service required

Output Params:

01. uuid: Unique User ID
02. from_station_name: From station name
03. from_station_id: From station id
04. to_station_name: To station name
05. to_station_id: To station id
06. train_date: Train date which was given in input
06. class: Class as 1st or 2nd; valid input: 1 or 2
07. amount: Amount/Person
08. total_amount: Total Amount
09. no_of_passenger: Number of passengers
10. special_service_flag: Special Service Flag
11. seat: Number of seats to reserve
12. food_service: Food Service required
13. luggage_service: Luggage Service required
14. connecting: Is the train connecting train
15. connecting_station: Connecting station name
16. connecting_station_id: Connecting station id
17. is_connection: Is the request valid
18. train_list_count: Number of options to choose from
19. path: path of train
20. train_list: List of train
20.1 	count_no: Count number
20.2 	train_time_id: train time ID
20.3 	train_id: train ID
20.4 	train_start_time: Train start time
20.5 	train_end_time: Train end time
20.6 	train_type: Train type as ICE or RE/B; type 1 = ICE and type 2 = RE/B
21. via: 1 when connecting train path
21.1 	train_list_count: Connecting number of options
21.2	path: path of train
21.3 	train_list: Connecting list of train
21.3.1 		count_no: count number
21.3.2 		train_time_id: train time ID
21.3.3 		train_id: train ID
21.3.4 		train_start_time: Train start time
21.3.5 		train_end_time: Train end time
21.3.6 		train_type: Train type as ICE or RE/B; type 1 = ICE and type 2 = RE/B

Test 1:
http://3.220.176.193/index.php/train/booking?uuid=abc&from_station=erfurt&to_station=frankfurt&date_time=20220615084505&class=2&no_of_passenger=2&special_service_flag=true&seats=2&food_service=true&luggage_service=false

Result:
{
    "uuid": "abc",
    "from_station_name": "erfurt",
    "from_station_id": "11",
    "to_station_name": "frankfurt",
    "to_station_id": "1",
    "class": "2",
    "amount": 52.5,
    "total_amount": 105,
    "no_of_passenger": "2",
    "special_service_flag": "true",
    "seats": "2",
    "food_service": "true",
    "luggage_service": "false",
    "connecting": 0,
    "is_connection": 1,
    "train_list_count": 4,
    "path": "start-erfurt-frankfurt-end",
    "train_list": [
        {
            "count_no": 0,
            "train_time_id": "102",
            "train_id": "1017",
            "train_start_time": "09:00:00",
            "train_end_time": "10:00:00",
            "train_type": "2"
        },
        {
            "count_no": 1,
            "train_time_id": "98",
            "train_id": "1016",
            "train_start_time": "10:00:00",
            "train_end_time": "11:00:00",
            "train_type": "1"
        },
        {
            "count_no": 2,
            "train_time_id": "103",
            "train_id": "1017",
            "train_start_time": "11:00:00",
            "train_end_time": "12:00:00",
            "train_type": "2"
        },
        {
            "count_no": 3,
            "train_time_id": "104",
            "train_id": "1017",
            "train_start_time": "13:00:00",
            "train_end_time": "14:00:00",
            "train_type": "2"
        }
    ]
}


Test 2:
http://3.220.176.193/index.php/train/booking?uuid=abc&from_station=erfurt&to_station=munich&date_time=20220630090105&class=2&no_of_passenger=2&special_service_flag=true&seats=2&food_service=true&luggage_service=false

Result:
{
    "uuid": "abc",
    "from_station_name": "erfurt",
    "from_station_id": "11",
    "to_station_name": "munich",
    "to_station_id": "5",
    "class": "2",
    "amount": 52.5,
    "total_amount": 588,
    "no_of_passenger": "2",
    "special_service_flag": "true",
    "seats": "2",
    "food_service": "true",
    "luggage_service": "false",
    "connecting": 1,
    "connecting_station": "frankfurt",
    "connecting_train_id": "1",
    "is_connection": 1,
    "train_list_count": 4,
    "path": "start-erfurt-frankfurt-end",
    "train_list": [
        {
            "count_no": 0,
            "train_time_id": "98",
            "train_id": "1016",
            "train_start_time": "10:00:00",
            "train_end_time": "11:00:00",
            "train_type": "1"
        },
        {
            "count_no": 1,
            "train_time_id": "103",
            "train_id": "1017",
            "train_start_time": "11:00:00",
            "train_end_time": "12:00:00",
            "train_type": "2"
        },
        {
            "count_no": 2,
            "train_time_id": "104",
            "train_id": "1017",
            "train_start_time": "13:00:00",
            "train_end_time": "14:00:00",
            "train_type": "2"
        },
        {
            "count_no": 3,
            "train_time_id": "105",
            "train_id": "1017",
            "train_start_time": "15:00:00",
            "train_end_time": "16:00:00",
            "train_type": "2"
        }
    ],
    "via": {
        "train_list_count": 4,
        "path": "start-frankfurt-stuttgart-augsburg-nurnburg-munich-end",
        "train_list": [
            {
                "count_no": 1,
                "train_time_id": "8",
                "train_id": "1002",
                "train_start_time": "12:00:00",
                "train_end_time": "14:00:00",
                "train_type": "2"
            },
            {
                "count_no": 2,
                "train_time_id": "2",
                "train_id": "1001",
                "train_start_time": "15:00:00",
                "train_end_time": "17:00:00",
                "train_type": "1"
            },
            {
                "count_no": 3,
                "train_time_id": "2",
                "train_id": "1001",
                "train_start_time": "15:00:00",
                "train_end_time": "17:00:00",
                "train_type": "1"
            },
            {
                "count_no": 4,
                "train_time_id": "10",
                "train_id": "1002",
                "train_start_time": "18:00:00",
                "train_end_time": "20:00:00",
                "train_type": "2"
            }
        ]
    }
}

Test 3: http://3.220.176.193/index.php/train/booking?uuid=abc&from_station=hamburg&to_station=munich&date_time=20220615090105&class=2&no_of_passenger=2&special_service_flag=true&seats=2&food_service=true&luggage_service=false

Result:
{
    "uuid": "abc",
    "from_station_name": "hamburg",
    "from_station_id": "3",
    "to_station_name": "munich",
    "to_station_id": "5",
    "class": "2",
    "amount": 252,
    "total_amount": 987,
    "no_of_passenger": "2",
    "special_service_flag": "true",
    "seats": "2",
    "food_service": "true",
    "luggage_service": "false",
    "connecting": 1,
    "connecting_station": "frankfurt",
    "connecting_train_id": "1",
    "is_connection": 1,
    "train_list_count": 4,
    "path": "start-hamburg-hannover-frankfurt-end",
    "train_list": [
        {
            "count_no": 0,
            "train_time_id": "75",
            "train_id": "1012",
            "train_start_time": "11:00:00",
            "train_end_time": "16:00:00",
            "train_type": "2"
        },
        {
            "count_no": 1,
            "train_time_id": "76",
            "train_id": "1012",
            "train_start_time": "13:00:00",
            "train_end_time": "18:00:00",
            "train_type": "2"
        },
        {
            "count_no": 2,
            "train_time_id": "70",
            "train_id": "1011",
            "train_start_time": "14:00:00",
            "train_end_time": "19:00:00",
            "train_type": "1"
        },
        {
            "count_no": 3,
            "train_time_id": "77",
            "train_id": "1012",
            "train_start_time": "15:00:00",
            "train_end_time": "20:00:00",
            "train_type": "2"
        }
    ],
    "via": {
        "train_list_count": 4,
        "path": "start-frankfurt-stuttgart-augsburg-nurnburg-munich-end",
        "train_list": [
            {
                "count_no": 1,
                "train_time_id": "10",
                "train_id": "1002",
                "train_start_time": "18:00:00",
                "train_end_time": "20:00:00",
                "train_type": "2"
            },
            {
                "count_no": 2,
                "train_time_id": "3",
                "train_id": "1001",
                "train_start_time": "20:00:00",
                "train_end_time": "22:00:00",
                "train_type": "1"
            },
            {
                "count_no": 3,
                "train_time_id": "3",
                "train_id": "1001",
                "train_start_time": "20:00:00",
                "train_end_time": "22:00:00",
                "train_type": "1"
            },
            {
                "count_no": 4,
                "train_time_id": "11",
                "train_id": "1002",
                "train_start_time": "22:00:00",
                "train_end_time": "00:00:00",
                "train_type": "2"
            }
        ]
    }
}

2. /equire/{uuid}

Link: http://3.220.176.193/index.php/train/enquire/ab123
Output:
{
    "uuid": "ab123",
    "status": "complete",
    "train_time_id": "99",
    "from_station_id": "11",
    "to_station_id": "1",
    "train_date": "2022-06-30",
    "train_start_time": "14:00:00",
    "train_end_time": "15:00:00",
    "class": "2",
    "booking_time": "2022-06-27 17:23:06",
    "connecting": "0",
    "payment_status": "COMPLETE",
    "amount": "105.00",
    "no_of_passenger": "2",
    "special_service_flag": true,
    "seats": "2",
    "seat_no": "1,2",
    "food_service": true,
    "luggage_service": true
}

POST:
1. /booking/{uuid}
01. uuid
02. train_time_id
03. from_station_id
04. to_station_id
05. train_start_time
06. train_end_time
07. train_date
08. class
09. booking_time = current_timestamp()
10. connecting
11. connecting_station_id
12. connecting_train_time_id
13. connecting_train_start_time
14. connecting_train_end_time
15. payment_status
16. amount
17. no_of_passenger
18. special_service_flag
19. seats
20. food_service
21. luggage_service

Output:
01. status: API call status
02. uuid
03. payment_status
04. special_service_flag
05. seat_no
06. via_seat_no

Test 1:
http://3.220.176.193/index.php/train/booking/abc123
Input:
{
	"train_time_id":99,
	"from_station_id":"11",
	"to_station_id":"1",
	"train_start_time":"14:40:00",
	"train_end_time":"15:00:00",
	"train_date":"2022-06-30",
	"class":2,
	"connecting":0,
	"payment_status":"INCOMPLETE",
	"amount":105,
	"no_of_passenger":3,
	"special_service_flag":true,
	"seats":3,
	"food_service": true,
	"luggage_service": true
}
Output:

{
    "status": "complete",
    "uuid": "abc123",
    "payment_status": "INCOMPLETE",
    "special_service_flag": true,
    "seat_no": "5,6,7"
}

Test 2:
http://3.220.176.193/index.php/train/booking/abc123

Input:
{
	"train_time_id":99,
	"from_station_id":11,
	"to_station_id":5,
	"train_start_time":"14:40:00",
	"train_end_time":"15:00:00",
	"train_date":"2022-06-30",
	"class":2,
	"connecting":1,
	"connecting_station_id": 1,
	"connecting_train_time_id": 9,
	"connecting_train_start_time": "16:00:00",
	"connecting_train_end_time": "18:00:00",
	"payment_status":"INCOMPLETE",
	"amount":588,
	"no_of_passenger":2,
	"special_service_flag":"true",
	"seats":2,
	"food_service": "true",
	"luggage_service": "false"
}

Output:
{
    "status": "complete",
    "uuid": "abc456",
    "payment_status": "INCOMPLETE",
    "special_service_flag": "true",
    "seat_no": "8,9",
    "via_seat_no": "3,4"
}


PUT:
1. /booking/{uuid}
Link: http://3.220.176.193/index.php/train/booking/ab123
Input:
{
	"payment_status": "COMPLETE",
	"special_service_flag": true,
	"seat_no": 2,
	"food_service": true,
	"luggage_service": true
}

Output:
{
    "uuid": "ab123",
    "status": "complete",
    "seat_no": "1,2"
}

DELETE:
1. /booking/{uuid}
Link: http://3.220.176.193/index.php/train/booking/ab123
Output:
{
    "uuid": "ab123",
    "status": "complete"
}

--------------------------------------------------- Required Database details to FE ---------------------------------------------------------
station 		
frankfurt 		
cologne			
hamburg			
berlin			
munich			
dortmund		
hannover		
stuttgart		
augsburg		
nurnberg		
erfurt			
leipzig			
