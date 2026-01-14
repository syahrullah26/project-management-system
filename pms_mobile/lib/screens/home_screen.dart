import 'package:flutter/material.dart';
import '../widgets/project_card.dart';
import 'package:velocity_x/velocity_x.dart';

extension HexColor on String {
  Color hexToColor() {
    final hexString = this.replaceAll('#', '');
    final hexInt = int.parse(hexString, radix: 16);
    return Color(hexInt | 0xFF000000);
  }
}

class HomeScreen extends StatelessWidget {
  const HomeScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Stack(
          children: [
            // OUTLINE
            Text(
              "PMS",
              style: TextStyle(
                fontSize: 28,
                fontWeight: FontWeight.w600,
                foreground: Paint()
                  ..style = PaintingStyle.stroke
                  ..strokeWidth = 3
                  ..color = "#000000".hexToColor(), // border color
              ),
            ),
            // FILL
            "PMS".text.xl2.semiBold.color("#4a3f35".hexToColor()).make(),
          ],
        ),
        backgroundColor: "#ffffe3".hexToColor(),
        elevation: 2,
      ),

      body: ListView(
        children: [
          ProjectCard(title: "Project", desc: ""),

          ProjectCard(title: "Reports", desc: ""),
        ],
      ),
    );
  }
}
