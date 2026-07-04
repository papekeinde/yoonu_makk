import 'package:flutter/material.dart';
import 'package:flutter_localizations/flutter_localizations.dart';
import 'package:provider/provider.dart';
import 'config/app_config.dart';
import 'config/theme.dart';
import 'config/routes.dart';
import 'controllers/auth_controller.dart';

void main() {
  WidgetsFlutterBinding.ensureInitialized();
  runApp(const YoonuJigeenApp());
}

class YoonuJigeenApp extends StatelessWidget {
  const YoonuJigeenApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MultiProvider(
      providers: [
        ChangeNotifierProvider(create: (_) => AuthController()),
      ],
      child: MaterialApp(
        title: AppConfig.appName,
        debugShowCheckedModeBanner: false,
        theme: AppTheme.theme,
        localizationsDelegates: const [
          GlobalMaterialLocalizations.delegate,
          GlobalWidgetsLocalizations.delegate,
          GlobalCupertinoLocalizations.delegate,
        ],
        supportedLocales: const [
          Locale('fr'),
          Locale('en'),
        ],
        locale: const Locale('fr'),
        initialRoute: Routes.landing,
        routes: Routes.all,
      ),
    );
  }
}