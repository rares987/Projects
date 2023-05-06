import random
import string
import sys
import os.path

length = random.randrange(12, 19)
alphanumeric = list(string.ascii_lowercase + string.ascii_uppercase + string.digits)
symbol = ['!', '?', '#', '@']
symbolCheck = False
password = ''
fileCheck = False

file = ''
if len(sys.argv) > 1:
    if sys.argv[1] != '-use_dict':
        print("Unknown command, try: -use_dict")
        quit()
    elif sys.argv[1] == '-use_dict':
        if len(sys.argv) < 3:
            print("Please provide a text file!")
            quit()
        file = sys.argv[2]
        if os.path.exists(file):
            if file.lower().endswith('.txt'):
                fileCheck = True
                if os.path.getsize(sys.argv[2]) == 0:
                    print("The file you provided doesn't contain any words! Please put some words there,then try again")
                    quit()
            else:
                print("The file you provided is not a .txt file! Please try again")
                quit()
        else:
            print("The file text does not exist! Please try again")
            quit()

if fileCheck is False:
    while len(password) != length:
        if len(password) == 0:
            password = password + random.choice(list(string.ascii_uppercase))
        elif len(password) + 1 == length and symbolCheck is False:
            password = password + random.choice(symbol)
            symbolCheck = True
        else:
            choice = random.randrange(1, 3)
            if choice == 1:  # put an alphanumeric
                password = password + random.choice(alphanumeric)
            else:  # put a symbol
                password = password + random.choice(symbol)
                symbolCheck = True

    print(password)
else:
    number_words = 0
    with open(file, 'r') as content:
        word_list = content.read().split()

    min_len = len(min(word_list, key=len))

    password = random.choice(word_list)
    wordWithValidStart = False
    for x in word_list:  # check if in file exists a word that starts with a letter
        if x[0].isalpha():
            wordWithValidStart = True
            break

    if wordWithValidStart is True:
        if min_len < 19:
            while password[0].isalpha() is False or len(password) > 18:
                password = random.choice(word_list)
            word_list.remove(password)
            password = password[0].upper() + password[1:]
    else:
        word_list.remove(password)
        password = random.choice(string.ascii_uppercase) + password[0:]

    for character in password:  # check if the word has illegal characters
        if character not in alphanumeric:
            if character not in symbol:
                password = password.replace(character, '')
        if character in symbol:
            symbolCheck = True

    WordBigger = False
    while len(password) > length:
        password = password[:len(password) - 1]
        WordBigger = True
    if WordBigger is True and symbolCheck is False:
        password = password[:len(password) - 1]
        password = password + random.choice(symbol)

    if len(min(word_list, key=len)) + len(
            password) > length:  # if all the words in file exceeds the length variable,
        # we put random alphanumeric
        while len(password) != length:
            if len(password) + 1 == length and symbolCheck is False:
                password = password + random.choice(symbol)
                symbolCheck = True
            else:
                choice = random.randrange(1, 3)
                if choice == 1:  # put an alphanumeric
                    password = password + random.choice(alphanumeric)
                else:  # put a symbol
                    password = password + random.choice(symbol)
                    symbolCheck = True

    listEmpty = False
    passwordContender = ''
    while len(password) < length:
        if len(word_list) != 0:
            passwordContender = random.choice(word_list)
            if len(passwordContender) + len(password) >= length:
                passwordContender = min(word_list, key=len)
            word_list.remove(passwordContender)
        else:
            listEmpty = True
        if listEmpty is False:
            if len(passwordContender) + len(
                    password) >= length:  # if all the words in file exceeds the length variable,
                # we put random alphanumeric
                while len(password) < length:
                    if len(password) + 1 == length and symbolCheck is False:
                        password = password + random.choice(symbol)
                        symbolCheck = True
                    else:
                        choice = random.randrange(1, 3)
                        if choice == 1:  # put an alphanumeric
                            password = password + random.choice(alphanumeric)
                        else:  # put a symbol
                            password = password + random.choice(symbol)
                            symbolCheck = True
            else:
                password = password + passwordContender
        else:
            while len(password) < length:
                if len(password) + 1 == length and symbolCheck is False:
                    password = password + random.choice(symbol)
                    symbolCheck = True
                else:
                    choice = random.randrange(1, 3)
                    if choice == 1:  # put an alphanumeric
                        password = password + random.choice(alphanumeric)
                    else:  # put a symbol
                        password = password + random.choice(symbol)
                        symbolCheck = True
            if symbolCheck is False and password == length:
                password = password[:len(password) - 1]
                password = password + random.choice(symbol)
            print(password)
            quit()

    if symbolCheck is False:
        password = password[:len(password) - 1]
        password = password + random.choice(symbol)
    print(password)
