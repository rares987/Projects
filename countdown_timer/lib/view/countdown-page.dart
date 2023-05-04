import 'package:countdown_timer/widget/round-button.dart';
import 'package:flutter/cupertino.dart';
import 'package:flutter/material.dart';
import 'package:flutter_ringtone_player/flutter_ringtone_player.dart';

class CountdownPage extends StatefulWidget {
  const CountdownPage({super.key});

  @override
  _CountdownPageState createState() => _CountdownPageState();
}

class _CountdownPageState extends State<CountdownPage> with TickerProviderStateMixin{

  Map data={};

  late AnimationController controller;

  bool isPlaying = false;

  String get countText{
    Duration count = controller.duration! * controller.value;
    return controller.isDismissed? '${controller.duration!.inHours}:'
    '${(controller.duration!.inMinutes % 60).toString().padLeft(2, '0')}:'
    '${(controller.duration!.inSeconds % 60).toString().padLeft(2, '0')}'
        :
    '${count.inHours}:'
    '${(count.inMinutes % 60).toString().padLeft(2, '0')}:'
    '${(count.inSeconds % 60).toString().padLeft(2, '0')}';
  }

  double progres_timer = 1.0;

  @override
  void initState() {
    super.initState();
    controller = AnimationController(
      vsync: this,
      duration: const Duration(seconds: 60
      ),
    );

    controller.addListener(() {
      if (countText == '0:00:01'){
        FlutterRingtonePlayer.playNotification();
      }

      if (!controller.isAnimating){
        setState(() {
          isPlaying = false;
          progres_timer = 1.0;
        });
      }else{
        setState(() {
          progres_timer = controller.value;
        });
      }
    });
  }

  @override
  void dispose() {
    controller.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {

    data = data.isNotEmpty ? data : ModalRoute.of(context)!.settings.arguments as Map; //get data from loading.dart
    //print(data);

    String bgImage = data['isDayTime'] ? 'day1.jpg' : 'night1.jpg';
    Color? bgColor = data['isDayTime'] ? Colors.cyan[100] : Colors.indigo[900];
    bool day = data['isDayTime'] ? true : false;

    return Scaffold(
      backgroundColor: bgColor,
      body: SafeArea(
        child: Container(
          decoration: BoxDecoration(
            image: DecorationImage(
              image: AssetImage('assets/$bgImage'),
              fit: BoxFit.cover,
            ),
          ),
          child: Column(
            children: [
               Expanded(
                child: Stack(
                  alignment: Alignment.center,
                  children: <Widget>[
                    SizedBox(
                      width: 250,
                        height: 250,
                        child: CircularProgressIndicator(
                          backgroundColor: day ? Colors.grey[200] : Colors.black,
                          color: Colors.yellow,
                          value: progres_timer,
                          strokeWidth: 10,
                        ),
                    ),
                    GestureDetector(
                      onTap: (){
                        if (controller.isDismissed){
                          showModalBottomSheet(context: context, builder: (context) =>
                              SizedBox(
                                  height: 300,
                                  child: CupertinoTimerPicker(
                                    initialTimerDuration: controller.duration!,
                                    onTimerDurationChanged: (time){
                                      setState(() {
                                        controller.duration = time;
                                      });
                                    },
                                  )
                              ),
                          );
                        }
                      },
                      child: AnimatedBuilder(
                        animation: controller,
                        builder: (context, child) => Text(
                          countText,
                          style: const TextStyle(
                            fontSize: 60,
                            fontWeight: FontWeight.bold,
                          ),
                        ),
                      ),
                    ),
                  ],
                ),
              ),
              Padding(
                padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 20),
                child: Row(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    GestureDetector(
                      onTap: () {
                        if (controller.isAnimating){
                          controller.stop();
                          setState(() {
                            isPlaying = false;
                          });
                        }else {
                          controller.reverse(
                              from: controller.value == 0 ? 1.0 : controller.value);
                          setState(() {
                            isPlaying = true;
                          });
                        }
                      },
                      child: RoundButton(
                          icon: isPlaying == true ? Icons.pause : Icons.play_arrow,
                      ),
                    ),
                    GestureDetector(
                      onTap: (){
                        controller.reset();
                        setState(() {
                          isPlaying = false;
                        });
                      },
                      child: const RoundButton(
                          icon: Icons.stop,
                      ),
                    ),
                    GestureDetector(
                      onTap: (){
                        Navigator.pushNamed(context, '/about');
                      },
                      child: const RoundButton(
                        icon: Icons.info_outline_rounded,
                      ),
                    ),
                  ],
                ),
              )
            ],
          ),
        ),
      ),
    );
  }
}