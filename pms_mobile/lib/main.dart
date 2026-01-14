import 'package:flutter/material.dart';
import 'package:velocity_x/velocity_x.dart';
import 'screens/home_screen.dart';

void main() {
  runApp(const MyApp());
}

class MyApp extends StatelessWidget {
  const MyApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      debugShowCheckedModeBanner: false,
      title: "PMS Mobile",
      home: HomeScreen(), // ← langsung ke homepage
      theme: ThemeData(colorSchemeSeed: Vx.blue600, useMaterial3: true),
    );
  }
}
