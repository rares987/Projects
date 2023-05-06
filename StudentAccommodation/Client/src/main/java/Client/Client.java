package Client;

import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStreamReader;
import java.io.PrintWriter;
import java.net.Socket;
import java.util.Scanner;

public class Client {

    public String response;

    public void run() {
        String serverAddress = "127.0.0.1"; // The server's IP address
        int PORT = 8100; // The server's port
        try (Socket socket = new Socket(serverAddress, PORT);
             PrintWriter out =
                     new PrintWriter(socket.getOutputStream(), true);
             BufferedReader in = new BufferedReader(
                     new InputStreamReader(socket.getInputStream()))) {
            // Send a request to the server
            boolean clientIsRunning = true;
            while (clientIsRunning) {
                Scanner scan = new Scanner(System.in);
                String request = scan.nextLine();
                if(response == "stop")
                {
                    clientIsRunning = false;
                }
                out.println(request);
                response = in.readLine();
            }
        } catch (IOException e) {
            System.err.println("No server listening... " + e);
        }
    }

    public void stop() {
        response = "stop";
    }
}
