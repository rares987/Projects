import 'package:flutter/material.dart';

class RoundButton extends StatelessWidget {
  const RoundButton({
    Key? key,
    required this.icon,
  }) : super(key: key);
  final IconData icon;

  @override
  Widget build(BuildContext context) {

    Map data = {};
    data = data.isNotEmpty ? data : ModalRoute.of(context)!.settings.arguments as Map; //get data from loading.dart
    Color? bgColor = data['isDayTime'] ? Colors.cyan[100] : Colors.indigo[900];

    return Padding(
      padding: const EdgeInsets.symmetric(
        horizontal: 5,
      ),
      child: CircleAvatar(
        radius: 30,
        backgroundColor: bgColor,
        child: Icon(
          icon,
          size: 36,

          color: Colors.black,
        ),
      ),
    );
  }
}