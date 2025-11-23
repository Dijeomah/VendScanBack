import 'package:flutter/material.dart';
import 'dart:async';
import '../utils/theme.dart';

class SplashScreen extends StatefulWidget {
  final Widget child;

  const SplashScreen({super.key, required this.child});

  @override
  State<SplashScreen> createState() => _SplashScreenState();
}

class _SplashScreenState extends State<SplashScreen> with TickerProviderStateMixin {
  late AnimationController _fadeController;
  late AnimationController _scaleController;
  late AnimationController _qrController;
  late Animation<double> _fadeAnimation;
  late Animation<double> _scaleAnimation;
  late Animation<double> _qrAnimation;

  bool _showSplash = true;

  @override
  void initState() {
    super.initState();

    // Fade animation
    _fadeController = AnimationController(
      duration: const Duration(milliseconds: 1000),
      vsync: this,
    );
    _fadeAnimation = Tween<double>(begin: 0.0, end: 1.0).animate(
      CurvedAnimation(parent: _fadeController, curve: Curves.easeIn),
    );

    // Scale animation
    _scaleController = AnimationController(
      duration: const Duration(milliseconds: 800),
      vsync: this,
    );
    _scaleAnimation = Tween<double>(begin: 0.5, end: 1.0).animate(
      CurvedAnimation(parent: _scaleController, curve: Curves.elasticOut),
    );

    // QR code animation (rotating squares)
    _qrController = AnimationController(
      duration: const Duration(milliseconds: 2000),
      vsync: this,
    );
    _qrAnimation = Tween<double>(begin: 0.0, end: 1.0).animate(
      CurvedAnimation(parent: _qrController, curve: Curves.easeInOut),
    );

    _startAnimations();
  }

  void _startAnimations() async {
    await Future.delayed(const Duration(milliseconds: 300));
    _fadeController.forward();
    _scaleController.forward();
    _qrController.forward();

    // Navigate after splash duration
    Timer(const Duration(milliseconds: 3000), () {
      if (mounted) {
        setState(() {
          _showSplash = false;
        });
      }
    });
  }

  @override
  void dispose() {
    _fadeController.dispose();
    _scaleController.dispose();
    _qrController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    if (!_showSplash) {
      return widget.child;
    }

    return Scaffold(
      body: Container(
        decoration: const BoxDecoration(
          gradient: LinearGradient(
            begin: Alignment.topLeft,
            end: Alignment.bottomRight,
            colors: [
              Color(0xFF667EEA),
              Color(0xFF764BA2),
              Color(0xFF9F7AEA),
            ],
          ),
        ),
        child: SafeArea(
          child: Center(
            child: FadeTransition(
              opacity: _fadeAnimation,
              child: ScaleTransition(
                scale: _scaleAnimation,
                child: Column(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    // Animated QR Code Icon
                    AnimatedBuilder(
                      animation: _qrAnimation,
                      builder: (context, child) {
                        return Stack(
                          alignment: Alignment.center,
                          children: [
                            // Pulsing circle background
                            Container(
                              width: 160 + (20 * _qrAnimation.value),
                              height: 160 + (20 * _qrAnimation.value),
                              decoration: BoxDecoration(
                                shape: BoxShape.circle,
                                color: Colors.white.withOpacity(0.1 * (1 - _qrAnimation.value)),
                              ),
                            ),
                            // Main container
                            Container(
                              width: 140,
                              height: 140,
                              padding: const EdgeInsets.all(20),
                              decoration: BoxDecoration(
                                color: Colors.white,
                                borderRadius: BorderRadius.circular(24),
                                boxShadow: [
                                  BoxShadow(
                                    color: Colors.black.withOpacity(0.2),
                                    blurRadius: 30,
                                    offset: const Offset(0, 10),
                                  ),
                                ],
                              ),
                              child: CustomPaint(
                                painter: QRCodePainter(
                                  progress: _qrAnimation.value,
                                  color: const Color(0xFF667EEA),
                                ),
                              ),
                            ),
                          ],
                        );
                      },
                    ),
                    const SizedBox(height: 40),
                    // App Name
                    ShaderMask(
                      shaderCallback: (bounds) => const LinearGradient(
                        colors: [Colors.white, Color(0xFFE2E8F0)],
                      ).createShader(bounds),
                      child: const Text(
                        'VendScan',
                        style: TextStyle(
                          fontSize: 48,
                          fontWeight: FontWeight.bold,
                          color: Colors.white,
                          letterSpacing: -1,
                        ),
                      ),
                    ),
                    const SizedBox(height: 12),
                    // Tagline
                    Text(
                      'Scan. Order. Enjoy.',
                      style: TextStyle(
                        fontSize: 16,
                        color: Colors.white.withOpacity(0.9),
                        letterSpacing: 2,
                        fontWeight: FontWeight.w300,
                      ),
                    ),
                    const SizedBox(height: 60),
                    // Loading indicator
                    SizedBox(
                      width: 40,
                      height: 40,
                      child: CircularProgressIndicator(
                        valueColor: AlwaysStoppedAnimation<Color>(
                          Colors.white.withOpacity(0.7),
                        ),
                        strokeWidth: 3,
                      ),
                    ),
                  ],
                ),
              ),
            ),
          ),
        ),
      ),
    );
  }
}

// Custom painter for animated QR code
class QRCodePainter extends CustomPainter {
  final double progress;
  final Color color;

  QRCodePainter({required this.progress, required this.color});

  @override
  void paint(Canvas canvas, Size size) {
    final paint = Paint()
      ..color = color
      ..style = PaintingStyle.fill;

    final squareSize = size.width / 5;
    final spacing = squareSize * 0.25;

    // Draw animated QR code pattern
    for (int row = 0; row < 5; row++) {
      for (int col = 0; col < 5; col++) {
        // Create a checkerboard pattern with some randomness
        final shouldDraw = (row + col) % 2 == 0 ||
                          (row == 2 && col == 2) ||
                          (row == 0 && col == 4) ||
                          (row == 4 && col == 0);

        if (shouldDraw) {
          final x = col * (squareSize + spacing);
          final y = row * (squareSize + spacing);

          // Animate squares appearing with delay based on position
          final delay = (row + col) / 10;
          final squareProgress = (progress - delay).clamp(0.0, 1.0);

          if (squareProgress > 0) {
            final currentSize = squareSize * squareProgress;
            final offset = (squareSize - currentSize) / 2;

            // Corner markers (larger squares)
            if ((row == 0 && col == 0) ||
                (row == 0 && col == 4) ||
                (row == 4 && col == 0)) {
              // Outer square
              canvas.drawRRect(
                RRect.fromRectAndRadius(
                  Rect.fromLTWH(x + offset, y + offset, currentSize, currentSize),
                  Radius.circular(4 * squareProgress),
                ),
                paint,
              );

              // Inner square (hollow)
              if (squareProgress > 0.5) {
                final innerPaint = Paint()
                  ..color = Colors.white
                  ..style = PaintingStyle.fill;

                final innerSize = currentSize * 0.4;
                final innerOffset = (currentSize - innerSize) / 2;

                canvas.drawRRect(
                  RRect.fromRectAndRadius(
                    Rect.fromLTWH(
                      x + offset + innerOffset,
                      y + offset + innerOffset,
                      innerSize,
                      innerSize,
                    ),
                    Radius.circular(2 * squareProgress),
                  ),
                  innerPaint,
                );
              }
            } else {
              // Regular squares
              canvas.drawRRect(
                RRect.fromRectAndRadius(
                  Rect.fromLTWH(x + offset, y + offset, currentSize, currentSize),
                  Radius.circular(2 * squareProgress),
                ),
                paint,
              );
            }
          }
        }
      }
    }
  }

  @override
  bool shouldRepaint(QRCodePainter oldDelegate) {
    return oldDelegate.progress != progress;
  }
}
