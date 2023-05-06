#include <graphics.h>
#include "UI.h"

stiva S;
coada C;
lista L;
lis_sim X;

int main()
{
    initwindow(1280, 720);  ///initializarea implicita se face pe rezolutia 1280x720
    meniu_principal(S,C,L,X,2,1,1); ///functia de pornire a meniului
    closegraph();
    return 0;
}

