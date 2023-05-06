#include <sys/types.h>
#include <sys/socket.h>
#include <netinet/in.h>
#include <unistd.h>
#include <stdio.h>
#include <string.h>
#include <stdlib.h>
#include <signal.h>
#include <pthread.h>
#include <iostream>
#include <sqlite3.h>

/* portul folosit */
#define PORT 2908

using namespace std;

typedef struct thData
{
    int idThread; // id-ul thread-ului tinut in evidenta de acest program
    int cl;       // descriptorul intors de accept
} thData;

static void *treat(void *); /* functia executata de fiecare thread ce realizeaza comunicarea cu clientii */
void raspunde(void *);
static int create_db(const char *s);
static int create_table(const char *s);
int callback(void *NotUsed, int argc, char **argv, char **azColName);
static int callback2(void *ans, int argc, char **argv, char **azColName);
static int insertIn_table(const char *s, char *username, char *password);
int check_user(const char *s, char *username);
int check_acc(const char *s, char *username, char *password);
int get_ID(const char *s, char *username);
static int insert_in_passwordsDB(const char *s, char *categorie, int id, char *title, char *url, char *password, char *username, char *notite);
static int update_DB(const char *s, int nr, int id, char *categorie, char *rezultat);
char *select_all(const char *s, int id);
char *select_all_for_category(const char *s, int id, char *categorie);
static int delete_DB(const char *s, int nr, int id);
int main()
{
    struct sockaddr_in server; // structura folosita de server
    struct sockaddr_in from;
    int nr; // mesajul primit de trimis la client
    int sd; // descriptorul de socket
    int pid;
    pthread_t th[100]; // Identificatorii thread-urilor care se vor crea
    int i = 0;

    if ((sd = socket(AF_INET, SOCK_STREAM, 0)) == -1)
    {
        perror("[server]Eroare la socket().\n");
        return -1;
    }
    int on = 1;
    setsockopt(sd, SOL_SOCKET, SO_REUSEADDR, &on, sizeof(on));

    bzero(&server, sizeof(server));
    bzero(&from, sizeof(from));
    server.sin_family = AF_INET;
    server.sin_addr.s_addr = htonl(INADDR_ANY);
    server.sin_port = htons(PORT);

    /* atasam socketul */
    if (bind(sd, (struct sockaddr *)&server, sizeof(struct sockaddr)) == -1)
    {
        perror("[server]Eroare la bind().\n");
        return -1;
    }

    /* punem serverul sa asculte daca vin clienti sa se conecteze */
    if (listen(sd, 5) == -1)
    {
        perror("[server]Eroare la listen().\n");
        return -1;
    }

    const char *dir = "database.db";
    create_db(dir);
    create_table(dir);
    /* servim in mod concurent clientii...folosind thread-uri */
    while (1)
    {
        int client;
        thData *td;
        socklen_t length;
        printf("[server]Asteptam la portul %d...\n", PORT);
        fflush(stdout);
        if ((client = accept(sd, (struct sockaddr *)&from, &length)) < 0)
        {
            perror("[server]Eroare la accept().\n");
            continue;
        }
        td = (struct thData *)malloc(sizeof(struct thData));
        td->idThread = i++;
        td->cl = client;
        pthread_create(&th[i], NULL, &treat, td);
    }
};
static void *treat(void *arg)
{
    struct thData tdL;
    tdL = *((struct thData *)arg);
    printf("[thread]- %d - Asteptam mesajul...\n", tdL.idThread);
    fflush(stdout);
    pthread_detach(pthread_self());
    raspunde((struct thData *)arg);
    /* am terminat cu acest client, inchidem conexiunea */
    close((intptr_t)arg);
    return (NULL);
};

void raspunde(void *arg)
{
    int nr = 0, i = 0, logat = 0;
    const char *dir = "database.db";
    struct thData tdL;
    tdL = *((struct thData *)arg);
    char username[30], parola[30];
    while (1)
    {
        if (!logat)
        {
            char mesaj[500] = "----------------\n1.Login\n2.Register\n3.Exit\n----------------\n\0";
            if (!nr)
            {

                if (write(tdL.cl, &mesaj, strlen(mesaj)) <= 0)
                {
                    printf("[Thread %d] ", tdL.idThread);
                    perror("[Thread]Eroare la write() catre client.\n");
                }

                if (read(tdL.cl, &nr, sizeof(int)) <= 0)
                {
                    return;
                }
            }
            if (nr == 1)
            {
                int ok = 1;
                do
                {
                    if (!ok)
                        strcpy(mesaj, "The username or password is incorrect, please provide another username:");
                    else
                        strcpy(mesaj, "Username:");
                    if (write(tdL.cl, &mesaj, sizeof(mesaj)) <= 0)
                    {
                        printf("[Thread %d] ", tdL.idThread);
                        perror("[Thread]Eroare la write() catre client.\n");
                    }
                    mesaj[0] = '\0';
                    if (read(tdL.cl, username, 256) <= 0)
                    {
                        printf("[Thread %d]\n", tdL.idThread);
                        perror("Eroare la read() de la client.\n");
                        return;
                    }

                    strcpy(mesaj, "Password:");
                    if (write(tdL.cl, &mesaj, strlen(mesaj)) <= 0)
                    {
                        printf("[Thread %d] ", tdL.idThread);
                        perror("[Thread]Eroare la write() catre client.\n");
                    }
                    mesaj[0] = '\0';
                    if (read(tdL.cl, parola, 256) <= 0)
                    {
                        printf("[Thread %d]\n", tdL.idThread);
                        perror("Eroare la read() de la client.\n");
                        return;
                    }
                    ok = check_acc(dir, username, parola);
                } while (!ok);
                if (ok)
                    logat = 1;
            }
            else if (nr == 2)
            {
                int ok = 0;
                do
                {
                    if (ok)
                        strcpy(mesaj, "Username already exists, please provide another username:");
                    else
                        strcpy(mesaj, "Username:");
                    if (write(tdL.cl, &mesaj, sizeof(mesaj)) <= 0)
                    {
                        printf("[Thread %d] ", tdL.idThread);
                        perror("[Thread]Eroare la write() catre client.\n");
                    }
                    mesaj[0] = '\0';
                    if (read(tdL.cl, username, 256) <= 0)
                    {
                        printf("[Thread %d]\n", tdL.idThread);
                        perror("Eroare la read() de la client.\n");
                        return;
                    }
                    ok = check_user(dir, username);
                } while (ok);
                strcpy(mesaj, "Password:");
                if (write(tdL.cl, &mesaj, strlen(mesaj)) <= 0)
                {
                    printf("[Thread %d] ", tdL.idThread);
                    perror("[Thread]Eroare la write() catre client.\n");
                }
                mesaj[0] = '\0';
                if (read(tdL.cl, parola, 256) <= 0)
                {
                    printf("[Thread %d]\n", tdL.idThread);
                    perror("Eroare la read() de la client.\n");
                    return;
                }
                insertIn_table(dir, username, parola);
                nr = 0;
            }
            else if (nr == 3)
            {
                return;
            }
            else
            {
                strcpy(mesaj, "Try again, the command does not exist");
                if (write(tdL.cl, &mesaj, sizeof(mesaj)) <= 0)
                {
                    printf("[Thread %d] ", tdL.idThread);
                    perror("[Thread]Eroare la write() catre client.\n");
                }
                if (read(tdL.cl, &nr, sizeof(int)) <= 0)
                {
                    return;
                }
            }
        }
        else /// logat
        {
            char mesaj[500] = "================\n1.Create new password\n2.See all passwords\n3.See all passwords for the category:\n4.Update a password\n5.Delete a password\n6.Exit\n================\n\0";
            if (write(tdL.cl, &mesaj, strlen(mesaj)) <= 0)
            {
                printf("[Thread %d] ", tdL.idThread);
                perror("[Thread]Eroare la write() catre client.\n");
            }

            if (read(tdL.cl, &nr, sizeof(int)) <= 0)
            {
                return;
            }
            if (nr == 1)
            {
                char categorie[50], title[50], url[50], parolaDB[50], usernameDB[50], notite[150];
                bzero(categorie, 50);
                bzero(title, 50);
                bzero(url, 50);
                bzero(parolaDB, 50);
                bzero(usernameDB, 50);
                bzero(notite, 150);
                int id;
                char mesaj[500] = "In which category do you want to save your password:";
                if (write(tdL.cl, &mesaj, strlen(mesaj)) <= 0)
                {
                    printf("[Thread %d] ", tdL.idThread);
                    perror("[Thread]Eroare la write() catre client.\n");
                }
                if (read(tdL.cl, categorie, 256) <= 0)
                {
                    printf("[Thread %d]\n", tdL.idThread);
                    perror("Eroare la read() de la client.\n");
                    return;
                }
                strcpy(mesaj, "Please insert your password:");
                if (write(tdL.cl, &mesaj, strlen(mesaj)) <= 0)
                {
                    printf("[Thread %d] ", tdL.idThread);
                    perror("[Thread]Eroare la write() catre client.\n");
                }
                if (read(tdL.cl, parolaDB, 256) <= 0)
                {
                    printf("[Thread %d]\n", tdL.idThread);
                    perror("Eroare la read() de la client.\n");
                    return;
                }
                id = get_ID(dir, username);
                insert_in_passwordsDB(dir, categorie, id, title, url, parolaDB, usernameDB, notite);
            }
            else if (nr == 2)
            {
                char mesaj[5000];
                strcpy(mesaj, select_all(dir, get_ID(dir, username)));

                if (write(tdL.cl, &mesaj, 5000) <= 0)
                {
                    printf("[Thread %d] ", tdL.idThread);
                    perror("[Thread]Eroare la write() catre client.\n");
                }
            }
            else if (nr == 3)
            {
                char mesaj[5000] = "Category:";
                char categorie[50];
                bzero(categorie, 50);
                if (write(tdL.cl, &mesaj, strlen(mesaj) + 1) <= 0)
                {
                    printf("[Thread %d] ", tdL.idThread);
                    perror("[Thread]Eroare la write() catre client.\n");
                }
                if (read(tdL.cl, categorie, 256) <= 0)
                {
                    printf("[Thread %d]\n", tdL.idThread);
                    perror("Eroare la read() de la client.\n");
                    return;
                }
                strcpy(mesaj, select_all_for_category(dir, get_ID(dir, username), categorie));
                if (write(tdL.cl, &mesaj, 5000) <= 0)
                {
                    printf("[Thread %d] ", tdL.idThread);
                    perror("[Thread]Eroare la write() catre client.\n");
                }
            }
            else if (nr == 4)
            {
                char mesaj[5000];
                strcpy(mesaj, select_all(dir, get_ID(dir, username)));
                strcat(mesaj, "Enter the NR of the password you want to update:");
                if (write(tdL.cl, &mesaj, 5000) <= 0)
                {
                    printf("[Thread %d] ", tdL.idThread);
                    perror("[Thread]Eroare la write() catre client.\n");
                }
                int NR, id;
                id = get_ID(dir, username);
                if (read(tdL.cl, &NR, sizeof(int)) <= 0)
                {
                    return;
                }
                char categorie[50], title[50], url[50], parolaDB[50], usernameDB[50], notite[150];
                bzero(categorie, 50);
                bzero(title, 50);
                bzero(url, 50);
                bzero(parolaDB, 50);
                bzero(usernameDB, 50);
                bzero(notite, 150);
                strcpy(mesaj, "Category:(Enter '-' if you don't want to change)");
                if (write(tdL.cl, &mesaj, strlen(mesaj) + 1) <= 0)
                {
                    printf("[Thread %d] ", tdL.idThread);
                    perror("[Thread]Eroare la write() catre client.\n");
                }
                if (read(tdL.cl, categorie, 256) <= 0)
                {
                    printf("[Thread %d]\n", tdL.idThread);
                    perror("Eroare la read() de la client.\n");
                    return;
                }
                if (categorie[0] != '-')
                {
                    char p[30];
                    strcpy(p, "CATEGORIE");
                    update_DB(dir, NR, id, p, categorie);
                }
                strcpy(mesaj, "Title:(Enter '-' if you don't want to change)");
                if (write(tdL.cl, &mesaj, strlen(mesaj) + 1) <= 0)
                {
                    printf("[Thread %d] ", tdL.idThread);
                    perror("[Thread]Eroare la write() catre client.\n");
                }
                if (read(tdL.cl, title, 256) <= 0)
                {
                    printf("[Thread %d]\n", tdL.idThread);
                    perror("Eroare la read() de la client.\n");
                    return;
                }
                if (title[0] != '-')
                {
                    char p[30];
                    strcpy(p, "TITLE");
                    update_DB(dir, NR, id, p, title);
                }
                strcpy(mesaj, "URL:(Enter '-' if you don't want to change)");
                if (write(tdL.cl, &mesaj, strlen(mesaj) + 1) <= 0)
                {
                    printf("[Thread %d] ", tdL.idThread);
                    perror("[Thread]Eroare la write() catre client.\n");
                }
                if (read(tdL.cl, url, 256) <= 0)
                {
                    printf("[Thread %d]\n", tdL.idThread);
                    perror("Eroare la read() de la client.\n");
                    return;
                }
                if (url[0] != '-')
                {
                    char p[30];
                    strcpy(p, "URL");
                    update_DB(dir, NR, id, p, url);
                }
                strcpy(mesaj, "Password:(Enter '-' if you don't want to change)");
                if (write(tdL.cl, &mesaj, strlen(mesaj) + 1) <= 0)
                {
                    printf("[Thread %d] ", tdL.idThread);
                    perror("[Thread]Eroare la write() catre client.\n");
                }
                if (read(tdL.cl, parolaDB, 256) <= 0)
                {
                    printf("[Thread %d]\n", tdL.idThread);
                    perror("Eroare la read() de la client.\n");
                    return;
                }
                if (parolaDB[0] != '-')
                {
                    char p[30];
                    strcpy(p, "PASSWORD");
                    update_DB(dir, NR, id, p, parolaDB);
                }
                strcpy(mesaj, "Username:(Enter '-' if you don't want to change)");
                if (write(tdL.cl, &mesaj, strlen(mesaj) + 1) <= 0)
                {
                    printf("[Thread %d] ", tdL.idThread);
                    perror("[Thread]Eroare la write() catre client.\n");
                }
                if (read(tdL.cl, usernameDB, 256) <= 0)
                {
                    printf("[Thread %d]\n", tdL.idThread);
                    perror("Eroare la read() de la client.\n");
                    return;
                }
                if (usernameDB[0] != '-')
                {
                    char p[30];
                    strcpy(p, "USERNAME");
                    update_DB(dir, NR, id, p, usernameDB);
                }
                strcpy(mesaj, "Notes:(Enter '-' if you don't want to change)");
                if (write(tdL.cl, &mesaj, strlen(mesaj) + 1) <= 0)
                {
                    printf("[Thread %d] ", tdL.idThread);
                    perror("[Thread]Eroare la write() catre client.\n");
                }
                if (read(tdL.cl, notite, 256) <= 0)
                {
                    printf("[Thread %d]\n", tdL.idThread);
                    perror("Eroare la read() de la client.\n");
                    return;
                }
                if (notite[0] != '-')
                {
                    char p[30];
                    strcpy(p, "NOTITE");
                    update_DB(dir, NR, id, p, notite);
                }
            }
            else if (nr == 5)
            {
                char mesaj[5000];
                strcpy(mesaj, select_all(dir, get_ID(dir, username)));
                strcat(mesaj, "Enter the NR of the password you want to delete:");
                if (write(tdL.cl, &mesaj, 5000) <= 0)
                {
                    printf("[Thread %d] ", tdL.idThread);
                    perror("[Thread]Eroare la write() catre client.\n");
                }
                int NR, id;
                id = get_ID(dir, username);
                if (read(tdL.cl, &NR, sizeof(int)) <= 0)
                {
                    return;
                }
                delete_DB(dir, NR, id);
            }
            else if (nr == 6)
            {
                return;
            }
        }
    }
}

static int create_db(const char *s)
{
    sqlite3 *db;
    int exit = 0;
    exit = sqlite3_open(s, &db);
    sqlite3_close(db);
    return 0;
}

static int create_table(const char *s)
{
    sqlite3 *db;
    char *err_msg = 0;
    int rc = sqlite3_open(s, &db);

    if (rc != SQLITE_OK)
    {

        fprintf(stderr, "Cannot open database: %s\n",
                sqlite3_errmsg(db));
        sqlite3_close(db);

        return 1;
    }

    string sql = /*"DROP TABLE IF EXISTS USERS;"
        "DROP TABLE IF EXISTS PASSWORDS;"*/
        "CREATE TABLE IF NOT EXISTS USERS (ID INTEGER PRIMARY KEY, Username CHAR(50), password CHAR(100));"
        "CREATE TABLE IF NOT EXISTS PASSWORDS (NR INTEGER PRIMARY KEY, Categorie CHAR(50), ID INT, Title CHAR(100), URL CHAR(100),PASSWORD CHAR(100), USERNAME CHAR(100), NOTITE CHAR(200) );";

    rc = sqlite3_exec(db, sql.c_str(), 0, 0, &err_msg);

    if (rc != SQLITE_OK)
    {

        fprintf(stderr, "SQL error: %s\n", err_msg);

        sqlite3_free(err_msg);
        sqlite3_close(db);

        return 1;
    }
    return 0;
}

int callback(void *ans, int argc, char **argv, char **azColName)
{

    char *answer = (char *)ans;

    for (int i = 0; i < argc; i++)
    {
        strcat(answer, azColName[i]);
        strcat(answer, " = ");
        strcat(answer, argv[i] ? argv[i] : "NULL");
        strcat(answer, "\n");
    }
    strcat(answer, "\n");
    return 0;
}

static int callback2(void *ans, int argc, char **argv, char **azColName)
{
    char *answer = (char *)ans;
    for (int i = 0; i < argc; i++)
    {
        strcat(answer, argv[i]);
        strcat(answer, ":");
    }
    return 0;
}

static int insertIn_table(const char *s, char *username, char *password)
{
    sqlite3 *db;
    char *err_msg = 0;
    int rc = sqlite3_open(s, &db);

    if (rc != SQLITE_OK)
    {

        fprintf(stderr, "Cannot open database: %s\n",
                sqlite3_errmsg(db));
        sqlite3_close(db);

        return 1;
    }

    string sql;
    char comanda[400];
    sprintf(comanda, "INSERT INTO USERS (Username, password) VALUES ('%s', '%s');", username, password);
    sql = comanda;
    rc = sqlite3_exec(db, sql.c_str(), 0, 0, &err_msg);

    if (rc != SQLITE_OK)
    {

        fprintf(stderr, "SQL error: %s\n", err_msg);

        sqlite3_free(err_msg);
        sqlite3_close(db);

        return 1;
    }
    return 0;
}

int check_user(const char *s, char *username)
{
    sqlite3 *db;
    char *err_msg = 0;
    int rc = sqlite3_open(s, &db);
    if (rc != SQLITE_OK)
    {

        fprintf(stderr, "Cannot open database: %s\n",
                sqlite3_errmsg(db));
        sqlite3_close(db);

        return -1;
    }
    string sql;
    char comanda[400];
    char result[10];
    bzero(result, 10);
    sprintf(comanda, "SELECT EXISTS(SELECT 1 FROM USERS WHERE username='%s');", username);
    sql = comanda;
    rc = sqlite3_exec(db, sql.c_str(), callback2, result, &err_msg);
    if (result[0] == '1')
        return 1;
    else
        return 0;
    if (rc != SQLITE_OK)
    {
        fprintf(stderr, "SQL error: %s\n", err_msg);
        sqlite3_free(err_msg);
        sqlite3_close(db);

        return -1;
    }
    return 0;
}

int check_acc(const char *s, char *username, char *password)
{
    sqlite3 *db;
    char *err_msg = 0;
    int rc = sqlite3_open(s, &db);

    if (rc != SQLITE_OK)
    {

        fprintf(stderr, "Cannot open database: %s\n",
                sqlite3_errmsg(db));
        sqlite3_close(db);

        return -1;
    }

    string sql;
    char comanda[400];
    char result[10];
    bzero(result, 10);
    sprintf(comanda, "SELECT EXISTS(SELECT 1 FROM USERS WHERE username='%s' and password='%s');", username, password);
    sql = comanda;
    rc = sqlite3_exec(db, sql.c_str(), callback2, result, &err_msg);
    if (result[0] == '1')
        return 1;
    else
        return 0;
    if (rc != SQLITE_OK)
    {

        fprintf(stderr, "SQL error: %s\n", err_msg);

        sqlite3_free(err_msg);
        sqlite3_close(db);

        return -1;
    }
    return 0;
}

int get_ID(const char *s, char *username)
{
    sqlite3 *db;
    char *err_msg = 0;
    int rc = sqlite3_open(s, &db);

    if (rc != SQLITE_OK)
    {

        fprintf(stderr, "Cannot open database: %s\n",
                sqlite3_errmsg(db));
        sqlite3_close(db);

        return -1;
    }

    char result[500];
    bzero(result, 500);
    char comanda[50];
    sprintf(comanda, "SELECT ID FROM USERS WHERE username='%s'", username);
    string sql = comanda;
    rc = sqlite3_exec(db, sql.c_str(), callback2, result, &err_msg);

    return atoi(result);
}

static int insert_in_passwordsDB(const char *s, char *categorie, int id, char *title, char *url, char *password, char *username, char *notite)
{
    sqlite3 *db;
    char *err_msg = 0;
    int rc = sqlite3_open(s, &db);

    if (rc != SQLITE_OK)
    {
        fprintf(stderr, "Cannot open database: %s\n",
                sqlite3_errmsg(db));
        sqlite3_close(db);
        return -1;
    }
    char comanda[4000];
    sprintf(comanda, "INSERT INTO PASSWORDS (Categorie,ID,Title,URL,Username, password,NOTITE) VALUES ('%s','%d','%s','%s','%s','%s','%s');", categorie, id, title, url, username, password, notite);
    string sql = comanda;
    rc = sqlite3_exec(db, sql.c_str(), 0, 0, &err_msg);

    if (rc != SQLITE_OK)
    {
        fprintf(stderr, "SQL error: %s\n", err_msg);
        sqlite3_free(err_msg);
        sqlite3_close(db);
        return 1;
    }
    return 0;
}

static int update_DB(const char *s, int nr, int id, char *categorie, char *rezultat)
{
    sqlite3 *db;
    char *err_msg = 0;
    int rc = sqlite3_open(s, &db);

    if (rc != SQLITE_OK)
    {
        fprintf(stderr, "Cannot open database: %s\n",
                sqlite3_errmsg(db));
        sqlite3_close(db);
        return -1;
    }
    char comanda[400];
    sprintf(comanda, "UPDATE PASSWORDS SET '%s' = '%s' WHERE NR = '%d' AND ID = '%d'", categorie, rezultat, nr, id);
    string sql = comanda;
    rc = sqlite3_exec(db, sql.c_str(), 0, 0, &err_msg);
    return 0;
}

char *select_all(const char *s, int id)
{
    sqlite3 *db;
    char *err_msg = 0;
    int rc = sqlite3_open(s, &db);

    if (rc != SQLITE_OK)
    {
        fprintf(stderr, "Cannot open database: %s\n",
                sqlite3_errmsg(db));
        sqlite3_close(db);
    }
    char comanda[400];
    sprintf(comanda, "SELECT NR,CATEGORIE,TITLE,URL,PASSWORD,USERNAME,NOTITE FROM PASSWORDS WHERE ID='%d'", id);
    string sql = comanda;
    char result[5000];
    bzero(result, 5000);
    rc = sqlite3_exec(db, sql.c_str(), callback, &result, &err_msg);
    char *p_result = strdup(result);
    return p_result;
}

char *select_all_for_category(const char *s, int id, char *categorie)
{
    sqlite3 *db;
    char *err_msg = 0;
    int rc = sqlite3_open(s, &db);

    if (rc != SQLITE_OK)
    {
        fprintf(stderr, "Cannot open database: %s\n",
                sqlite3_errmsg(db));
        sqlite3_close(db);
    }
    char comanda[400];
    sprintf(comanda, "SELECT NR,CATEGORIE,TITLE,URL,PASSWORD,USERNAME,NOTITE FROM PASSWORDS WHERE ID='%d' AND CATEGORIE = '%s'", id, categorie);
    string sql = comanda;
    char result[5000];
    bzero(result, 5000);
    rc = sqlite3_exec(db, sql.c_str(), callback, &result, &err_msg);
    char *p_result = strdup(result);
    return p_result;
}

static int delete_DB(const char *s, int nr, int id)
{
    sqlite3 *db;
    char *err_msg = 0;
    int rc = sqlite3_open(s, &db);

    if (rc != SQLITE_OK)
    {
        fprintf(stderr, "Cannot open database: %s\n",
                sqlite3_errmsg(db));
        sqlite3_close(db);
        return -1;
    }
    char comanda[400];
    sprintf(comanda, "DELETE FROM PASSWORDS WHERE NR = '%d' AND ID = '%d'", nr, id);
    string sql = comanda;
    rc = sqlite3_exec(db, sql.c_str(), 0, 0, &err_msg);
    return 0;
}