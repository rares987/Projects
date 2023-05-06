#include <sys/types.h>
#include <sys/socket.h>
#include <netinet/in.h>
#include <unistd.h>
#include <stdio.h>
#include <stdlib.h>
#include <netdb.h>
#include <string.h>
#include <arpa/inet.h>
#include <iostream>
#include <sqlite3.h>

using namespace std;

int port;
int main(int argc, char *argv[])
{
    int sd;
    struct sockaddr_in server;
    int nr = 0;
    char buf[10];
    char mesaj[5000];

    if (argc != 3)
    {
        printf("Sintaxa: %s <adresa_server> <port>\n", argv[0]);
        return -1;
    }

    port = atoi(argv[2]);

    if ((sd = socket(AF_INET, SOCK_STREAM, 0)) == -1)
    {
        perror("Eroare la socket().\n");
        return -1;
    }

    server.sin_family = AF_INET;
    server.sin_addr.s_addr = inet_addr(argv[1]);
    server.sin_port = htons(port);

    if (connect(sd, (struct sockaddr *)&server, sizeof(struct sockaddr)) == -1)
    {
        perror("[client]Eroare la connect().\n");
        return -1;
    }

    while (1)
    {
        bzero(mesaj, 5000);
        if (read(sd, mesaj, 5000) < 0)
        {
            perror("[client]Eroare la read() de la server.\n");
            return -1;
        }
        fflush(stdout);
        cout << mesaj << '\n';
        if (mesaj[0] == '-' || mesaj[0] == '=' || !strcmp(mesaj, "Try again, the command does not exist") ||
            strstr(mesaj, "Enter the NR of the password you want to update:") != NULL ||
            strstr(mesaj, "Enter the NR of the password you want to delete:") != NULL)
        {
            printf("Introduceti un numar: ");
            fflush(stdout);
            cin >> nr;
            if (write(sd, &nr, sizeof(int)) <= 0)
            {
                perror("[client]Eroare la write() spre server.\n");
                return -1;
            }
            if ((nr == 3 && mesaj[0] == '-') || (nr == 6 && mesaj[0] == '='))
            {
                cout << "Bye Bye!\n";
                close(sd);
                return 0;
            }
        }
        else if (!strcmp(mesaj, "Username:") || !strcmp(mesaj, "Password:") ||
                 !strcmp(mesaj, "Username already exists, please provide another username:") ||
                 !strcmp(mesaj, "The username or password is incorrect, please provide another username:") ||
                 !strcmp(mesaj, "In which category do you want to save your password:") ||
                 !strcmp(mesaj, "Please insert your password:") ||
                 strstr(mesaj, "Category:") != NULL || strstr(mesaj, "Title:") != NULL || strstr(mesaj, "URL:") != NULL || strstr(mesaj, "Password:") != NULL || strstr(mesaj, "Username:") != NULL || strstr(mesaj, "Notes:") != NULL)
        {
            cin.getline(mesaj, 5000);
            if (strlen(mesaj) == 0)
                cin.getline(mesaj, 5000);

            mesaj[strlen(mesaj)] = '\0';
            if (write(sd, &mesaj, strlen(mesaj) + 1) <= 0)
            {
                perror("[client]Eroare la write() spre server.\n");
                return -1;
            }
        }
    }
    close(sd);
}