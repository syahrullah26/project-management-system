import 'package:flutter/material.dart';
import 'package:velocity_x/velocity_x.dart';

class ProjectCard extends StatelessWidget {
  final String title;
  final String desc;

  const ProjectCard({super.key, required this.title, required this.desc});

  @override
  Widget build(BuildContext context) {
    if (title == "Project") {
      return VxBox(
            child: VStack([
              title.text.xl.bold.make(),
              desc.text.blue800.make().py8(),
            ]),
          ).white.rounded
          .border(color: const Color.fromARGB(255, 113, 159, 228))
          .shadowXs
          .p16
          .make()
          .p8();
    } else {
      return VxBox(
            child: VStack([
              title.text.xl.bold.make(),
              desc.text.red800.make().py8(),
            ]),
          ).white.rounded
          .border(color: const Color.fromARGB(255, 228, 113, 113))
          .shadowXs
          .p16
          .make()
          .p8();
    }
  }
}
