import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'theme/app_theme.dart';
import 'services/database_service.dart';
import 'services/auth_service.dart';
import 'viewmodels/auth_viewmodel.dart';
import 'viewmodels/admin_dashboard_viewmodel.dart';
import 'viewmodels/student_requests_viewmodel.dart';
import 'viewmodels/create_request_viewmodel.dart';
import 'views/auth/login_screen.dart';

void main() {
  WidgetsFlutterBinding.ensureInitialized();
  runApp(const CampusConnectApp());
}

class CampusConnectApp extends StatelessWidget {
  const CampusConnectApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MultiProvider(
      providers: [
        // Services Layer
        ChangeNotifierProvider<DatabaseService>(
          create: (_) => DatabaseService(),
        ),
        ChangeNotifierProxyProvider<DatabaseService, AuthService>(
          create: (context) => AuthService(context.read<DatabaseService>()),
          update: (context, db, previous) => previous ?? AuthService(db),
        ),

        // ViewModels Layer (MVVM Pattern)
        ChangeNotifierProxyProvider2<AuthService, DatabaseService, AuthViewModel>(
          create: (context) => AuthViewModel(
            context.read<AuthService>(),
            context.read<DatabaseService>(),
          ),
          update: (context, auth, db, previous) => previous ?? AuthViewModel(auth, db),
        ),
        ChangeNotifierProxyProvider<DatabaseService, AdminDashboardViewModel>(
          create: (context) => AdminDashboardViewModel(context.read<DatabaseService>()),
          update: (context, db, previous) => previous ?? AdminDashboardViewModel(db),
        ),
        ChangeNotifierProxyProvider2<DatabaseService, AuthService, StudentRequestsViewModel>(
          create: (context) => StudentRequestsViewModel(
            context.read<DatabaseService>(),
            context.read<AuthService>(),
          ),
          update: (context, db, auth, previous) => previous ?? StudentRequestsViewModel(db, auth),
        ),
        ChangeNotifierProxyProvider2<DatabaseService, AuthService, CreateRequestViewModel>(
          create: (context) => CreateRequestViewModel(
            context.read<DatabaseService>(),
            context.read<AuthService>(),
          ),
          update: (context, db, auth, previous) => previous ?? CreateRequestViewModel(db, auth),
        ),
      ],
      child: MaterialApp(
        title: 'Campus Connect Mobile',
        debugShowCheckedModeBanner: false,
        theme: AppTheme.lightTheme,
        home: const LoginScreen(),
      ),
    );
  }
}
