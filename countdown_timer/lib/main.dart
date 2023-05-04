import 'package:flutter/material.dart';
import 'view/countdown-page.dart';
import 'view/loading.dart';
import 'view/about.dart';

void main() {
  runApp(MaterialApp(
    initialRoute: '/',
    routes: {
      '/': (context) => Loading(),
      '/home': (context) => CountdownPage(),
      '/about': (context) => About()
    },
  ));
}

class MyApp extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      debugShowCheckedModeBanner: false,
      title: 'Flutter Countdown app',
      theme: ThemeData(
        primarySwatch: Colors.blue,
      ),
      home: const CountdownPage(),
    );
  }
}