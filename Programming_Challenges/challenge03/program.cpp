/*	Michael Erdenberger
	Programming Challenges
	Challenge 03		   */

#include <iostream>
#include <string>
#include <vector>

using namespace std;

int 	binarySearch(vector<int> &, int target);
void	response(int index, int numCols, int target); 

int main(int argc, char *argv[]) {

	int numRows, numCols;
	while (cin >> numRows >> numCols) {

		if (numRows == 0 || numCols == 0) { continue; }

		int tmp;
		vector<int> matrix; 
		for (int i = 0; i < numRows*numCols; i++) {
			cin >> tmp;
			matrix.push_back(tmp);
		}

		int numTargets, target, index;
		cin >> numTargets;
		for (int i = 0; i < numTargets; i++) {
			cin >> target;
			response(binarySearch(matrix, target), numCols, target);
		}
	}

	return 0;
}

int binarySearch(vector<int> &matrix, int target) {
	int start	= 0;
	int end		= matrix.size() - 1; 
	while (start <= end) {
		int middle = (start + end)/2;
		if (matrix[middle] == target) {
			return middle;
		} else if (matrix[middle] > target) {
			end = middle - 1;
		} else {
			start = middle + 1;
		}
	}
	return -1;
}

void response(int index, int numCols, int target) {
	int row, col;
	if (index < 0) {
		cout << target << " is not in the grid\n";
	} else {
		row = index/numCols;
		col = index % numCols;
		cout << target << " is at row=" << row << ", col=" << col << endl;
	}
}
