import 'package:flutter/material.dart';
import '../constants/app_colors.dart';

/// Interior Page Hero Banner matching the webapp's header banner:
/// Dark navy/teal overlay over background crowd texture + centered white bold title.
class PageHeroBanner extends StatelessWidget {
  final String title;
  final double height;

  const PageHeroBanner({
    super.key,
    required this.title,
    this.height = 110.0,
  });

  @override
  Widget build(BuildContext context) {
    return Container(
      width: double.infinity,
      height: height,
      decoration: const BoxDecoration(
        gradient: LinearGradient(
          begin: Alignment.topCenter,
          end: Alignment.bottomCenter,
          colors: [
            Color(0xFF013A40),
            Color(0xFF0F172A),
          ],
        ),
      ),
      child: Center(
        child: Text(
          title,
          style: const TextStyle(
            fontSize: 22,
            fontWeight: FontWeight.w700,
            color: Colors.white,
            letterSpacing: 0.5,
          ),
          textAlign: TextAlign.center,
        ),
      ),
    );
  }
}
