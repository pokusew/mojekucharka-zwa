// see https://github.com/jhamlet/svg-react-loader/issues/104#issuecomment-429767794
// see https://stackoverflow.com/questions/51725002/false-positive-typescript-cannot-find-module-warning
declare module '*.png' {

	const value: string;

	export default value;

}
