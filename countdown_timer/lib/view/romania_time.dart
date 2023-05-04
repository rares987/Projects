import 'package:http/http.dart';
import 'dart:convert';
import 'package:intl/intl.dart';

class WorldTime{
  late String time;
  late bool isDaytime;

  Future<void> getTime() async {
    Response response =  await get(Uri.parse('http://worldtimeapi.org/api/timezone/Europe/Bucharest'));
    Map data = jsonDecode(response.body);
    //print(data);
    String datetime = data['datetime'];
    String offset = data['utc_offset'].substring(1,3);

    DateTime now = DateTime.parse(datetime);
    now = now.add(Duration(hours: int.parse(offset)));

    isDaytime = now.hour > 6 && now.hour < 15;  //day time 6 AM intre 9PM
    time = DateFormat.jm().format(now);
  }

}
