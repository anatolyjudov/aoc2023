map = {
    "1": "1", "2": "2", "3": "3", "4": "4", "5": "5", "6": "6", "7": "7", "8": "8", "9": "9",
    "one": "1", "two": "2", "three": "3", "four": "4", "five": "5", "six": "6", "seven": "7", "eight": "8", "nine": "9" 
}

sum = 0

with open('input.txt', 'r') as file:
    for line in file:
        d1 = d2 = ""

        p = 0
        digit_found = False
        while not digit_found:
            for k, v in map.items():
                if line.startswith(k, p):
                    d1 = v
                    digit_found = True
                    break
            p = p + 1

        p = len(line) - 1
        digit_found = False
        while not digit_found:
            for k, v in map.items():
                if line.endswith(k, 0, p):
                    d2 = v
                    digit_found = True
                    break
            p = p - 1

        sum = sum + int(d1 + d2)

print(sum)