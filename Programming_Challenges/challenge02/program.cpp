/* Michael Erdenberger
   Programming Challenges
   challenge02			*/

#include <iostream>
#include <stack>
#include <string>
#include <cmath>
#include <queue>
#include <sstream>

using namespace std;

string process_stream(queue<string> &ops); 
void display_solution(string solution);
        
int main(int argc, char *argv[]) {
	
	string line;
	while (getline(cin, line)) {
	
		queue<string> ops;
    	istringstream iss(line);
    	for(string line; iss >> line; ) {
        	ops.push(line);
		}
		
		string solution = process_stream(ops);

		display_solution(solution);	
	}
	return 0;
}


string process_stream(queue<string> &ops) {
   
        stack<int> operands;
        int operand1, operand2;
        while (!ops.empty()) {
            string curr = ops.front();
            ops.pop();
            if (curr.length() == 1) {
                switch (curr[0]) {
                    case '+' :
                        operand1 = operands.top();
                        operands.pop();
                        operand2 = operands.top();
                        operands.pop();
                        operands.push(operand1 + operand2);
                        break;
                    case '-' : 
                        operand1 = operands.top();
                        operands.pop(); 
                        operand2 = operands.top();
                        operands.pop();
                        operands.push(operand2 - operand1);
                        break;
                    case '*' :
                        operand1 = operands.top();
                        operands.pop();
                        operand2 = operands.top();  
                        operands.pop();
                        operands.push(operand1 * operand2);
                        break;
                    case '/' :
                        operand1 = operands.top();
                        operands.pop();
                        operand2 = operands.top();
                        operands.pop();
                        operands.push(operand2 / operand1);
                        break;
                    case '^' :
                        operand1 = operands.top();
                        operands.pop();
                        operand2 = operands.top();
                        operands.pop();
                        operands.push(pow(operand2, operand1));
                        break;
                    default :
                        operands.push(curr[0] - '0');
                        break;
                }
            } else {
                operands.push(stoi(curr));
            }
        }
        return to_string(operands.top());
}

void display_solution(string solution) {

        int len = solution.length()*3;
        char line1[len], line2[len], line3[len];
        for (int j = 0; j < len; j++) {
            line1[j] = ' ';
            line2[j] = ' ';
            line3[j] = ' ';
        }
        for (int i = 0; i < solution.length(); i++) {
            int x = i*3;
            switch (solution[i]) {
                case '0' :
                    line1[x+1]  = '_';
                    line2[x]    = '|';
                    line2[x+2]  = '|';
                    line3[x]    = '|';
                    line3[x+1]  = '_';
                    line3[x+2]  = '|';
                    break;
                case '1' :
                    line2[x+2]  = '|';
                    line3[x+2]  = '|';
                    break;
                case '2' :
                    line1[x+1]  = '_';
                    line2[x+1]  = '_';
                    line2[x+2]  = '|';
                    line3[x]    = '|';
                    line3[x+1]  = '_';
                    break;
                case '3' :
                    line1[x+1]  = '_';
                    line2[x+1]  = '_';
                    line2[x+2]  = '|';
                    line3[x+1]  = '_';
                    line3[x+2]  = '|';
                    break;
                case '4' :
                    line2[x]    = '|';
                    line2[x+1]  = '_';
                    line2[x+2]  = '|';
                    line3[x+2]  = '|';
                    break;
                case '5' :
                    line1[x+1]  = '_';
                    line2[x]    = '|';
                    line2[x+1]  = '_';
                    line3[x+2]  = '|';
                    line3[x+1]  = '_';
                    break;
                case '6' :
                    line1[x+1]  = '_';
                    line2[x]    = '|';
                    line2[x+1]  = '_';
                    line3[x]    = '|';
                    line3[x+1]  = '_';
                    line3[x+2]  = '|';
                    break;
                case '7' :
                    line1[x+1]  = '_';
                    line2[x+2]  = '|';
                    line3[x+2]  = '|';
                    break;
                case '8' :
                    line1[x+1]  = '_';
                    line2[x]    = '|';
                    line2[x+1]  = '_';
                    line2[x+2]  = '|';
                    line3[x]    = '|';
                    line3[x+1]  = '_';
                    line3[x+2]  = '|';
                    break;
                case '9' :
                    line1[x+1]  = '_';
                    line2[x]    = '|';
                    line2[x+1]  = '_';
                    line2[x+2]  = '|';
                    line3[x+1]  = '_';
                    line3[x+2]  = '|';
                    break;
                case '-' :
                    line2[x+1]  = '_';
                    break;
            }
        }
        // Output
        for (int i = 0; i < len; i++) {
            cout << line1[i];
        }
        cout << endl;
        for (int i = 0; i < len; i++) {
            cout << line2[i];
        }
        cout << endl;
        for (int i = 0; i < len; i++) {
            cout << line3[i];
        }
        cout << endl;
}
